<?php

namespace Database\Factories;

use App\Enums\BookingTokenType;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingToken>
 */
class BookingTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'type' => BookingTokenType::CANCEL,
            'token' => $this->faker->unique()->sha256(),
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'used_at' => null,
        ];
    }
}
