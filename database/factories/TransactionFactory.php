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
            'transaction_id' => $this->faker->uuid(),
            'payment_method' => 'barion',
            'metadata' => [],
        ];
    }
}
