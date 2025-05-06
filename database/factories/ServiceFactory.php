<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->randomElement([
            'Desenvolvimento de Software',
            'Consultoria em TI',
            'Suporte Técnico',
            'Gestão de Projetos',
            'Segurança da Informação',
            'Infraestrutura de Redes',
            'Serviços em Nuvem',
            'Desenvolvimento Web',
            'Desenvolvimento Mobile',
            'Análise de Dados'
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'image' => 'services/service_' . $this->faker->numberBetween(1, 3) . '.png',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}