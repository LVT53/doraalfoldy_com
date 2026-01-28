<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'category_id' => ServiceCategory::factory(),
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'duration_minutes' => $this->faker->numberBetween(30, 120),
            'buffer_minutes' => $this->faker->numberBetween(0, 30),
            'price' => $this->faker->numberBetween(5000, 50000) / 100,
            'deposit_fee' => $this->faker->numberBetween(1000, 10000) / 100,
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
