<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduleException>
 */
class ScheduleExceptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exception_date' => $this->faker->dateTime(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_off' => false,
        ];
    }
}
