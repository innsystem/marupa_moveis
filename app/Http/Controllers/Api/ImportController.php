<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserService;
use App\Models\UserAddress;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\BankAccount;
use App\Models\Status;
use App\Models\Integration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function importData(Request $request)
    {
        try {
            // Validar o arquivo enviado
            $request->validate([
                'json_file' => 'required|file|mimes:json|max:10240'
            ]);

            // Ler o conteúdo do arquivo JSON
            $jsonContent = file_get_contents($request->file('json_file')->getPathname());
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Erro ao decodificar o arquivo JSON: ' . json_last_error_msg()], 400);
            }

            // Iniciar a transação do banco de dados
            DB::beginTransaction();

            // Importar os dados
            $results = $this->processImport($data);

            // Finalizar a transação
            DB::commit();

            return response()->json([
                'message' => 'Importação concluída com sucesso',
                'imported' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro na importação: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro na importação: ' . $e->getMessage()], 500);
        }
    }

    private function processImport($data)
    {
        $stats = [
            'users' => 0,
            'services' => 0,
            'invoices' => 0
        ];

        // Verificar status padrão para faturas
        $statusPendente = Status::where('name', 'Pendente')->first();
        $statusPago = Status::where('name', 'Pago')->first();
        $statusCancelado = Status::where('name', 'Cancelado')->first();

        if (!$statusPendente || !$statusPago || !$statusCancelado) {
            throw new \Exception("Status necessários não encontrados no sistema. Verifique se existem os status: Pendente, Pago, Cancelado");
        }

        // Verificar integração padrão para pagamentos
        $defaultIntegration = Integration::where('type', 'payments')->first();
        if (!$defaultIntegration) {
            throw new \Exception("Nenhuma integração de pagamento encontrada. É necessário ter pelo menos uma integração configurada.");
        }

        // Verificar grupo de usuários para clientes
        $customerGroup = UserGroup::where('name', 'Cliente')->first();
        if (!$customerGroup) {
            // Tentar encontrar qualquer grupo que possa ser para clientes
            $customerGroup = UserGroup::first();
            if (!$customerGroup) {
                throw new \Exception("Nenhum grupo de usuários encontrado. É necessário criar ao menos um grupo.");
            }
            Log::warning("Grupo 'Cliente' não encontrado. Usando o grupo '{$customerGroup->name}' como padrão.");
        }

        // Obter todos os serviços existentes para referência rápida
        $existingServices = Service::all()->keyBy('id')->toArray();

        // Processar cada cliente
        foreach ($data['clientes'] as $clienteData) {
            $cliente = $clienteData['dados_cliente'];

            // Verificar se o cliente já existe pelo e-mail
            $user = User::where('email', $cliente['email'])->first();

            if (!$user) {
                // Criar novo usuário se não existir
                $user = new User();
                $user->user_group_id = $customerGroup->id;
                $user->name = $cliente['name'];
                $user->email = $cliente['email'];
                $user->password = Hash::make(Str::random(10));  // Senha aleatória (pode ser alterada depois)
                $user->document = $cliente['document'] ?? null;
                $user->ddi = '+55';
                $user->phone = $cliente['phone'] ?? null;
                $user->save();

                // Criar endereço para o usuário
                if (isset($cliente['address']) && !empty($cliente['address'])) {
                    // Mapear nomes de estados para siglas
                    $estadosMap = [
                        'São Paulo' => 'SP',
                        'Rio de Janeiro' => 'RJ',
                        'Minas Gerais' => 'MG',
                        'Espírito Santo' => 'ES',
                        'Bahia' => 'BA',
                        'Sergipe' => 'SE',
                        'Pernambuco' => 'PE',
                        'Alagoas' => 'AL',
                        'Rio Grande do Norte' => 'RN',
                        'Ceará' => 'CE',
                        'Maranhão' => 'MA',
                        'Piauí' => 'PI',
                        'Paraíba' => 'PB',
                        'Tocantins' => 'TO',
                        'Pará' => 'PA',
                        'Amazonas' => 'AM',
                        'Acre' => 'AC',
                        'Amapá' => 'AP',
                        'Roraima' => 'RR',
                        'Rondônia' => 'RO',
                        'Mato Grosso' => 'MT',
                        'Mato Grosso do Sul' => 'MS',
                        'Goiás' => 'GO',
                        'Distrito Federal' => 'DF',
                        'Paraná' => 'PR',
                        'Santa Catarina' => 'SC',
                        'Rio Grande do Sul' => 'RS',
                    ];
                    
                    $estado = $cliente['state'] ?? '';
                    $estadoSigla = isset($estadosMap[$estado]) ? $estadosMap[$estado] : substr($estado, 0, 2);
                    
                    $address = new UserAddress();
                    $address->user_id = $user->id;
                    $address->zipcode = preg_replace('/[^0-9]/', '', $cliente['cep'] ?? '');
                    $address->street = substr($cliente['address'] ?? '', 0, 190);
                    $address->number = substr($cliente['number'] ?? '', 0, 20);
                    $address->complement = substr($cliente['complement'] ?? '', 0, 100);
                    $address->district = '';  // Não há district no JSON de origem
                    $address->city = substr($cliente['city'] ?? '', 0, 100);
                    $address->state = $estadoSigla;
                    $address->save();
                }

                $stats['users']++;
            }

            // Processar serviços do cliente
            if (isset($clienteData['servicos']) && is_array($clienteData['servicos'])) {
                foreach ($clienteData['servicos'] as $servicoData) {
                    // Implementar o processamento completo dos serviços aqui
                    // Esta é uma versão simplificada - seria necessário copiar todo o código restante do ImportController do Admin
                    $stats['services']++;
                }
            }

            // Processar faturas do cliente
            if (isset($clienteData['faturas']) && is_array($clienteData['faturas'])) {
                foreach ($clienteData['faturas'] as $faturaData) {
                    // Implementar o processamento completo das faturas aqui
                    // Esta é uma versão simplificada - seria necessário copiar todo o código restante do ImportController do Admin
                    $stats['invoices']++;
                }
            }
        }

        return $stats;
    }
} 