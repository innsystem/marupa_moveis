<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportReleasesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:releases {file : Caminho para o arquivo releases.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa lançamentos financeiros (releases) para transações';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("O arquivo {$filePath} não existe.");
            return 1;
        }

        $this->info("Importando dados do arquivo {$filePath}...");

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar o arquivo JSON: ' . json_last_error_msg());
            return 1;
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($data['releases'] as $release) {
                $lanc = $release['dados_lancamento'];
                $conta = $release['detalhes_conta_bancaria'];

                // Verifica se a conta bancária já existe pelo nome
                $bankAccount = BankAccount::where('bank_name', $conta['name'])->first();
                if (!$bankAccount) {
                    $bankAccount = new BankAccount();
                    $bankAccount->user_id = 1; // Ajuste se necessário
                    $bankAccount->bank_name = $conta['name'];
                    $bankAccount->saldo = $conta['amount'] ?? 0;
                    $bankAccount->account_type = $conta['type'] ?? 'private';
                    $bankAccount->created_at = $conta['created_at'] ?? now();
                    $bankAccount->updated_at = $conta['updated_at'] ?? now();
                    $bankAccount->save();
                }

                // Converter type
                $type = $lanc['type'] == 1 ? 'income' : 'expense';

                // Tentar extrair o número da fatura do campo name
                $invoiceId = null;
                if (preg_match('/Fatura #(\d+)/', $lanc['name'], $matches)) {
                    $possibleInvoiceId = $matches[1];
                    // Verifica se a fatura existe
                    if (\App\Models\Invoice::find($possibleInvoiceId)) {
                        $invoiceId = $possibleInvoiceId;
                    }
                }

                // Criar transação
                $transaction = new Transaction();
                $transaction->type = $type;
                $transaction->amount = $lanc['amount'];
                $transaction->gateway_fee = 0;
                $transaction->description = $lanc['name'];
                $transaction->date = $lanc['date'] ?? now();
                $transaction->created_at = $lanc['created_at'] ?? now();
                $transaction->updated_at = $lanc['updated_at'] ?? now();
                if ($invoiceId) {
                    $transaction->invoice_id = $invoiceId;
                }
                $transaction->save();
                $count++;
            }
            DB::commit();
            $this->info("Importação concluída! {$count} lançamentos importados.");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro na importação: ' . $e->getMessage());
            return 1;
        }
    }
} 