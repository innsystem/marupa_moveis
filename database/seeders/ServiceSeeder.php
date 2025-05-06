<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'id' => 1,
                'title' => 'Comercial',
                'slug' => 'comercial',
                'description' => 'Nosso time comercial atua estrategicamente desde o início, analisando a viabilidade técnica dos projetos e propondo soluções construtivas adequadas. Mantemos uma comunicação clara entre todos os envolvidos — cliente, arquitetos e construtoras — assegurando o alinhamento completo de expectativas e cronogramas.',
                'image' => 'services/service_1.png',
                'status' => 1,
                'sort_order' => 1,
                'is_recurring' => false,
                'single_payment_price' => 0.00,
                'monthly_price' => 0.00,
                'quarterly_price' => 0.00,
                'semiannual_price' => 0.00,
                'annual_price' => 0.00,
                'biennial_price' => 0.00
            ],
            [
                'id' => 2,
                'title' => 'Engenharia',
                'slug' => 'engenharia',
                'description' => 'No núcleo técnico, transformamos conceitos em desenhos executivos precisos. Todos os projetos são desenvolvidos com base nas especificações recebidas do cliente/arquiteto e atualizados conforme as condições reais da obra. Após a aprovação técnica do cliente/arquiteto, o projeto segue para execução.',
                'image' => 'services/service_2.png',
                'status' => 1,
                'sort_order' => 2,
                'is_recurring' => false,
                'single_payment_price' => 0.00,
                'monthly_price' => 0.00,
                'quarterly_price' => 0.00,
                'semiannual_price' => 0.00,
                'annual_price' => 0.00,
                'biennial_price' => 0.00
            ],
            [
                'id' => 3,
                'title' => 'Produção',
                'slug' => 'producao',
                'description' => 'Operamos em uma planta industrial moderna e automatizada, com layout otimizado e tecnologia de ponta. Essa estrutura permite uma produção ágil, padronizada e com altíssimo nível de acabamento.',
                'image' => 'services/service_3.png',
                'status' => 1,
                'sort_order' => 3,
                'is_recurring' => false,
                'single_payment_price' => 0.00,
                'monthly_price' => 0.00,
                'quarterly_price' => 0.00,
                'semiannual_price' => 0.00,
                'annual_price' => 0.00,
                'biennial_price' => 0.00
            ],
        ];

        foreach ($services as $serviceData) {
            // Verificar se o serviço já existe
            $service = Service::find($serviceData['id']);

            if (!$service) {
                Service::create($serviceData);
            } else {
                // Se já existe, atualizar
                $service->update($serviceData);
            }
        }
    }
}
