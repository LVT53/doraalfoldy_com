<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferencePhoto>
 */
class ReferencePhotoFactory extends Factory
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
            'image_path' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->sentence(),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
