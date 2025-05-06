<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Integration;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = Invoice::all();
        $integrations = Integration::where('type', 'payments')->all();

        foreach ($invoices as $invoice) {
            Transaction::create([
                'invoice_id' => $invoice->id,
                'integration_id' => $integrations->random()->id,
                'type' => 'income',
                'amount' => $invoice->total,
                'gateway_fee' => rand(1, 10),
                'description' => 'Pagamento recebido para a fatura ' . $invoice->id,
                'date' => now(),
            ]);
        }
    }
}
