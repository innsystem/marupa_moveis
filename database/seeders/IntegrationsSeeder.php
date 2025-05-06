<?php

namespace Database\Seeders;

use App\Models\Integration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IntegrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            [
                'name' => 'Send Pulse',
                'slug' => 'send-pulse',
                'description' => 'Ferramenta SMTP utilizada para o envio de e-mail transacionais.',
                'type' => 'communication',
                'settings' => [
                    "site_name" => "InnSystem V2",
                    "site_email" => "contato@innsystem.com.br",
                    "client_id" => "b5ee542704980cd49f503fb1914d077c",
                    "client_secret" => "5a21fae30e1c56a41accfa7855f233f2",
                    "id_template" => "290254"
                ],
                'status' => 1
            ],

            [
                'name' => 'Google Analytics',
                'slug' => 'google-analytics',
                'description' => 'Ferramenta de Análise para obter números e relatórios do site.',
                'type' => 'analytics',
                'settings' => null,
                'status' => 2
            ],
            [
                'name' => 'WhatsApp API',
                'slug' => 'whatsapp-api',
                'description' => 'Ferramenta de envio de notificações no whatsapp.',
                'type' => 'communication',
                'settings' => ["host" => 'https://evolution.integreai.com.br/message/sendText/innv2', "token" => 'AFC34EC154DF-4996-BC51-B88B9AF488F3'],
                'status' => 1
            ],

            [
                'name' => 'Mercado Pago',
                'slug' => 'mercadopago',
                'description' => 'Integração com Meio de Pagamento por cartão de crédito, boleto e pix.',
                'type' => 'payments',
                'settings' => [
                    "status_pix" => "1",
                    "fee_pix" => "0.99",
                    "status_boleto" => "0",
                    "fee_boleto" => "3.49",
                    "status_credit_card" => "1",
                    "fee_credit_card" => "4.98",
                    "access_token" => "APP_USR-1616954816199672-062817-152046b603195e8789b3fe61bdc0f6da-91042568",
                    "fee_installment" => "0",
                    "max_installments" => "12",
                    "installments_free" => "2",
                    "webhook_url" => "https://integrations.innsystem.com.br/mercadopago/webhook"
                ],
                'status' => 1
            ],

            [
                'name' => 'PagSeguro',
                'slug' => 'pagseguro',
                'description' => 'Integração com Meio de Pagamento por cartão de crédito, boleto e pix.',
                'type' => 'payments',
                'settings' => null,
                'status' => 1
            ],

            [
                'name' => 'Pagamento no Local',
                'slug' => 'pagamento-no-local',
                'description' => 'Recebimento de pagamento no Local.',
                'type' => 'payments',
                'settings' => null,
                'status' => 1
            ],

            [
                'name' => 'Shopee',
                'slug' => 'shopee',
                'description' => 'Integração com Shopee para obter links de produtos e afiliados.',
                'type' => 'marketplaces',
                'settings' => null,
                'status' => 1
            ],
        ];

        foreach ($integrations as $integration) {
            Integration::create($integration);
        }
    }
}
