<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PortfolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'status' => 1,
            'location' => $this->faker->city . ', ' . $this->faker->stateAbbr . ', ' . $this->faker->country,
            'type' => $this->faker->randomElement(['Residencial', 'Comercial', 'Hotel', 'Resort', 'Corporativo', 'Industrial']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
