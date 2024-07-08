<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Monitor;
use App\Models\Penetasan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PeternakSeeder::class,
            MasterSeeder::class,
            // PenetasanSeeder::class,
        ]);
        // Monitor::factory(500)->create();

    }
}
