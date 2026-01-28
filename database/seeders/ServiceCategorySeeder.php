<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceCategory::create([
            'name' => 'Szempilla',
            'slug' => 'szempilla',
            'description' => 'Szempilla szolgáltatások',
            'sort_order' => 1,
        ]);

        ServiceCategory::create([
            'name' => 'Smink',
            'slug' => 'smink',
            'description' => 'Smink szolgáltatások',
            'sort_order' => 2,
        ]);

        ServiceCategory::create([
            'name' => 'Szemöldök',
            'slug' => 'szemoldok',
            'description' => 'Szemöldök szolgáltatások',
            'sort_order' => 3,
        ]);
    }
}
