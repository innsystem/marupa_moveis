<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'logo', 'value' => null],
            ['key' => 'favicon', 'value' => null],
            ['key' => 'meta_title', 'value' => 'Bem-vindo | Marupa Móveis'],
            ['key' => 'meta_keywords', 'value' => 'móveis, marcenaria, decoração, marupa móveis'],
            ['key' => 'meta_description', 'value' => 'Marupa Móveis - Qualidade e sofisticação em móveis sob medida.'],
            ['key' => 'script_head', 'value' => ''],
            ['key' => 'script_body', 'value' => ''],
            ['key' => 'site_name', 'value' => 'Marupa Móveis'],
            ['key' => 'site_proprietary', 'value' => 'Marupa Móveis LTDA'],
            ['key' => 'site_document', 'value' => '98.765.432/0001-12'],
            ['key' => 'site_email', 'value' => 'contato@marupamoveis.com.br'],
            ['key' => 'telephone', 'value' => '(11) 4002-8922'],
            ['key' => 'cellphone', 'value' => '(11) 98888-7777'],
            ['key' => 'address', 'value' => 'Rua das Marupás, 456, São Paulo, SP'],
            ['key' => 'hour_open', 'value' => '09:00 às 18:00'],
            ['key' => 'client_id', 'value' => Str::uuid()],
            ['key' => 'client_secret', 'value' => Str::random(40)],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
