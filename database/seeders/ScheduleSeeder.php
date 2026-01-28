<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Monday - Friday: 9:00 - 17:00
        for ($day = 0; $day < 5; $day++) {
            Schedule::create([
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_off' => false,
            ]);
        }

        // Saturday: 9:00 - 14:00
        Schedule::create([
            'day_of_week' => 5,
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'is_off' => false,
        ]);

        // Sunday: off
        Schedule::create([
            'day_of_week' => 6,
            'start_time' => '00:00:00',
            'end_time' => '00:00:00',
            'is_off' => true,
        ]);
    }
}
