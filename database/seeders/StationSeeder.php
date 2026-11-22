<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    /**
     * Seed the default stations.
     */
    public function run(): void
    {
        foreach (['Station 1', 'Station 2', 'Station 3'] as $name) {
            Station::firstOrCreate(['name' => $name]);
        }
    }
}
