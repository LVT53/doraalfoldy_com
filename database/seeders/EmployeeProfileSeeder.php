<?php

namespace Database\Seeders;

use App\Models\EmployeeProfile;
use Illuminate\Database\Seeder;

class EmployeeProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmployeeProfile::create([
            'name' => 'Dóra Álfoldy',
            'bio' => 'Szépségszakember',
            'image_path' => null,
            'instagram_url' => null,
        ]);
    }
}
