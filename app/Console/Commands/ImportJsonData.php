<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\Integration;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Status;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserGroup;
use App\Models\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:json {file : Path to the JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("O arquivo {$filePath} não existe.");
            return 1;
        }
        
        $this->info("Importando dados do arquivo {$filePath}...");
        
        try {
            // Ler conteúdo do arquivo JSON
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Erro ao decodificar o arquivo JSON: ' . json_last_error_msg());
                return 1;
            }
            
            // Iniciar transação
            DB::beginTransaction();
            
            // Importar dados
            $results = $this->processImport($data);
            
            // Finalizar transação
            DB::commit();
            
            $this->info("Importação concluída com sucesso!");
            $this->info("Importados: {$results['users']} clientes, {$results['services']} serviços, {$results['invoices']} faturas.");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro na importação: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
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
        $statusAtivo = Status::where('name', 'Ativo')->first();

        if (!$statusPendente || !$statusPago || !$statusCancelado) {
            throw new \Exception("Status necessários não encontrados no sistema. Verifique se existem os status: Pendente, Pago, Cancelado");
        }

        // Verificar integração padrão para pagamentos
        $defaultIntegration = Integration::where('type', 'payments')->first();
        if (!$defaultIntegration) {
            throw new \Exception("Nenhuma integração de pagamento encontrada. É necessário ter pelo menos uma integração configurada.");
        }

        // Verificar grupo de usuários para clientes
        $customerGroup = UserGroup::where('name', 'Customer')->first();
        if (!$customerGroup) {
            // Tentar encontrar qualquer grupo que possa ser para clientes
            $customerGroup = UserGroup::first();
            if (!$customerGroup) {
                throw new \Exception("Nenhum grupo de usuários encontrado. É necessário criar ao menos um grupo.");
            }
            $this->warn("Grupo 'Cliente' não encontrado. Usando o grupo '{$customerGroup->name}' como padrão.");
        }

        // Obter todos os serviços existentes para referência rápida
        $existingServices = Service::all()->keyBy('id')->toArray();

        $this->output->progressStart(count($data['clientes']));

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
                $user->created_at = $cliente['created_at'] ?? now();
                $user->updated_at = $cliente['updated_at'] ?? now();
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
                    $address->is_default = true;
                    $address->created_at = $cliente['created_at'] ?? now();
                    $address->updated_at = $cliente['updated_at'] ?? now();
                    $address->save();
                }

                $stats['users']++;
            }

            // Processar serviços do cliente
            if (isset($clienteData['servicos']) && is_array($clienteData['servicos'])) {
                foreach ($clienteData['servicos'] as $servicoData) {
                    $servicoCliente = $servicoData['dados_servico_cliente'];
                    $servicoDetalhes = $servicoData['detalhes_servico'];

                    // Verificar se o serviço existe
                    $service = Service::find($servicoDetalhes['id']);
                    if (!$service) {
                        // Mapear serviços antigos para novos
                        $serviceIdMap = [
                            1 => 1,  // Loja Virtual - Starting
                            2 => 2,  // Hospedagem de Site
                            3 => 3,  // Catálogo Virtual
                            4 => 4,  // Loja Virtual - Rocket
                            5 => 5,  // Internet
                            6 => 6,  // WhatsApp
                        ];
                        
                        // Verificar se existe um mapeamento para o ID do serviço
                        $mappedServiceId = $serviceIdMap[$servicoDetalhes['id']] ?? null;
                        
                        if ($mappedServiceId) {
                            $service = Service::find($mappedServiceId);
                        }
                        
                        // Se ainda não encontrou o serviço, cria um novo
                        if (!$service) {
                            // Criar o serviço se não existir
                            $service = new Service();
                            $service->title = $servicoDetalhes['name'];
                            $service->slug = Str::slug($servicoDetalhes['name']);
                            $service->description = $servicoDetalhes['description'];
                            $service->status = 1; // Ativo
                            $service->sort_order = 0;
                            $service->is_recurring = $servicoDetalhes['period'] == 'recorrente';
                            $service->single_payment_price = 0.00;
                            $service->monthly_price = $servicoDetalhes['price'] ?? 0.00;
                            $service->quarterly_price = $servicoDetalhes['price_trimestral'] ?? 0.00;
                            $service->semiannual_price = 0.00;
                            $service->annual_price = $servicoDetalhes['price_anual'] ?? 0.00;
                            $service->biennial_price = 0.00;
                            $service->save();
                        }

                        $stats['services']++;
                    }

                    // Mapear o período de serviço antigo para o novo formato
                    $periodMap = [
                        'mensal' => 'monthly',
                        'trimestral' => 'quarterly',
                        'anual' => 'annual',
                        'recorrente' => 'monthly',
                    ];

                    // Verificar se já existe um serviço de usuário com esse ID
                    $userService = UserService::find($servicoCliente['id']);
                    if (!$userService) {
                        // Determinar o status apropriado
                        $serviceStatus = $statusAtivo->id; // Ativo por padrão
                        if ($servicoCliente['status'] == 'cancelado') {
                            $serviceStatus = $statusCancelado->id;
                        } else if ($servicoCliente['status'] == 'ativo') {
                            $serviceStatus = $statusAtivo->id; // Ativo
                        }

                        // Criar o serviço do usuário
                        $userService = new UserService();
                        $userService->id = $servicoCliente['id']; // Manter o mesmo ID
                        $userService->user_id = $user->id;
                        $userService->service_id = $service->id;
                        $userService->start_date = Carbon::parse($servicoCliente['date_start'])->format('Y-m-d');
                        $userService->end_date = Carbon::parse($servicoCliente['date_end'])->format('Y-m-d');
                        $userService->price = $servicoCliente['price'];
                        $userService->period = $periodMap[$servicoCliente['period']] ?? 'monthly';
                        $userService->status = $serviceStatus;
                        
                        // Metadata para domínio e outros dados específicos
                        $metadata = [
                            'domain' => $servicoCliente['dominio'] ?? null
                        ];
                        $userService->metadata = $metadata;
                        $userService->created_at = $servicoCliente['created_at'] ?? now();
                        $userService->updated_at = $servicoCliente['updated_at'] ?? now();
                        $userService->save();
                    }
                }
            }

            // Processar faturas do cliente
            if (isset($clienteData['faturas']) && is_array($clienteData['faturas'])) {
                foreach ($clienteData['faturas'] as $faturaData) {
                    $fatura = $faturaData['dados_fatura'];
                    $contaBancaria = $faturaData['detalhes_conta_bancaria'] ?? null;

                    // Verificar se a fatura já existe pelo ID
                    $invoice = Invoice::find($fatura['id']);
                    if (!$invoice) {
                        // Mapear status
                        $invoiceStatus = $statusPendente->id; // Pendente por padrão
                        if ($fatura['status'] == 'pago') {
                            $invoiceStatus = $statusPago->id;
                        } else if ($fatura['status'] == 'cancelado') {
                            $invoiceStatus = $statusCancelado->id;
                        }

                        // Criar a fatura
                        $invoice = new Invoice();
                        $invoice->id = $fatura['id']; // Manter o mesmo ID
                        $invoice->user_id = $user->id;
                        $invoice->integration_id = $defaultIntegration->id;
                        $invoice->method_type = 'pix'; // Método padrão
                        $invoice->total = $fatura['price'];
                        $invoice->status = $invoiceStatus;
                        $invoice->due_at = Carbon::parse($fatura['date_end'])->format('Y-m-d');
                        
                        if ($fatura['date_payment']) {
                            $invoice->paid_at = Carbon::parse($fatura['date_payment'])->format('Y-m-d H:i:s');
                        }
                        
                        $invoice->created_at = $fatura['created_at'] ?? now();
                        $invoice->updated_at = $fatura['updated_at'] ?? now();
                        $invoice->save();

                        // Extrair IDs de serviços vinculados à fatura
                        $serviceIds = [];
                        if (!empty($fatura['customer_service_id'])) {
                            if (is_string($fatura['customer_service_id'])) {
                                // Se for string como "[10]", extrair os números
                                preg_match_all('/\d+/', $fatura['customer_service_id'], $matches);
                                $serviceIds = $matches[0] ?? [];
                            } elseif (is_array($fatura['customer_service_id'])) {
                                $serviceIds = $fatura['customer_service_id'];
                            }
                        }

                        // Processar descrição
                        $descriptions = $fatura['description'];
                        if (is_string($descriptions)) {
                            // Se for uma string JSON, decodificá-la
                            if (strpos($descriptions, '[') === 0) {
                                $descriptions = json_decode($descriptions, true);
                            } else {
                                $descriptions = [$descriptions];
                            }
                        }

                        if (!is_array($descriptions)) {
                            $descriptions = [$descriptions];
                        }

                        // Criar itens de fatura
                        foreach ($descriptions as $index => $description) {
                            $invoiceItem = new InvoiceItem();
                            $invoiceItem->invoice_id = $invoice->id;
                            $invoiceItem->description = $description;
                            $invoiceItem->quantity = 1;
                            $invoiceItem->price_unit = $fatura['price'];
                            $invoiceItem->price_total = $fatura['price'];

                            // Vincular ao serviço de usuário, se disponível
                            if (isset($serviceIds[$index])) {
                                $invoiceItem->target_type = 'user_service';
                                $invoiceItem->target_id = $serviceIds[$index];
                            }

                            $invoiceItem->created_at = $fatura['created_at'] ?? now();
                            $invoiceItem->updated_at = $fatura['updated_at'] ?? now();
                            $invoiceItem->save();
                        }

                        $stats['invoices']++;
                    }
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        return $stats;
    }
} 