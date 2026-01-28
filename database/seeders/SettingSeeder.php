<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('cancellation_hours', '24');
        Setting::set('reminder_hours', '24');
        Setting::set('default_buffer_minutes', '0');
        Setting::set('site_name', 'Dóra Álfoldy');
        Setting::set('admin_email', '');
        Setting::set('booking_terms', '');
        Setting::set('barion_pos_key', '');
        Setting::set('barion_sandbox', 'true');
        Setting::set('slot_lock', 'lock_row');
    }
}
