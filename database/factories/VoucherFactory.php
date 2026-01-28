<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('????-####'),
            'type' => 'percentage',
            'value' => $this->faker->numberBetween(5, 50),
            'balance' => 0,
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+365 days'),
            'used_at' => null,
        ];
    }
}
