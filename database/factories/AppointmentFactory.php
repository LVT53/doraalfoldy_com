<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $service = Service::factory()->create();

        return [
            'service_id' => $service->id,
            'voucher_id' => null,
            'user_name' => $this->faker->name(),
            'user_email' => $this->faker->safeEmail(),
            'user_phone' => $this->faker->phoneNumber(),
            'start_time' => $startTime,
            'end_time' => (clone $startTime)->modify("+{$service->duration_minutes} minutes"),
            'buffer_at_booking' => $service->buffer_minutes,
            'status' => AppointmentStatus::PENDING,
            'price_at_booking' => $service->price,
            'deposit_at_booking' => $service->deposit_fee,
            'voucher_discount' => 0,
            'notes' => $this->faker->sentence(),
            'locale' => 'hu',
            'reminder_sent_at' => null,
        ];
    }
}
