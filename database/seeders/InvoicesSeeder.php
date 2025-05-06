<?php

namespace Database\Seeders;

use App\Models\Integration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = [
            [
                'user_id' => 3,
                'integration_id' => Integration::where('slug', 'mercadopago')->first()->id,
                'method_type' => 'credit_card',
                'total' => 100.00,
                'status' => 24,
                'due_at' => \Carbon\Carbon::parse('2025-02-10')->format('Y-m-d'),
                'items' => [
                    [
                        'description' => 'Service 1',
                        'quantity' => 1,
                        'price_unit' => 100.00,
                        'price_total' => 100.00,
                    ],
                ],
            ],
            [
                'user_id' => 3,
                'integration_id' => Integration::where('slug', 'mercadopago')->first()->id,
                'method_type' => 'pix',
                'total' => 270.00,
                'status' => 23,
                'due_at' => \Carbon\Carbon::parse('2025-02-10')->format('Y-m-d'),
                'items' => [
                    [
                        'description' => 'Service 2',
                        'quantity' => 2,
                        'price_unit' => 100.00,
                        'price_total' => 200.00,
                    ],
                    [
                        'description' => 'Extra 2',
                        'quantity' => 1,
                        'price_unit' => 35.00,
                        'price_total' => 70.00,
                    ],
                ],
            ],
            [
                'user_id' => 4,
                'integration_id' => Integration::where('slug', 'mercadopago')->first()->id,
                'method_type' => 'pix',
                'total' => 70.00,
                'status' => 23,
                'due_at' => \Carbon\Carbon::parse('2025-02-10')->format('Y-m-d'),
                'items' => [
                    [
                        'description' => 'Extra 3',
                        'quantity' => 1,
                        'price_unit' => 35.00,
                        'price_total' => 70.00,
                    ],
                ],
            ],
        ];

        foreach ($invoices as $invoice) {
            $items = $invoice['items'];
            unset($invoice['items']);

            $invoice = \App\Models\Invoice::create($invoice);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }
        }
    }
}
