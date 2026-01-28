<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payable_type' => 'App\Models\Appointment',
            'payable_id' => 1,
            'amount' => $this->faker->numberBetween(1000, 50000) / 100,
            'status' => 'pending',
            'payment_id' => $this->faker->uuid(),
            'barion_status' => 'Prepared',
        ];
    }
}
