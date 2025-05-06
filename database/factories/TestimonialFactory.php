<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'avatar' => null,
            'content' => $this->faker->text(80),
            'rating' => $this->faker->numberBetween(1, 5),
            'localization' => $this->faker->city,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
