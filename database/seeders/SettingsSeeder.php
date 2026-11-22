<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the default application settings.
     */
    public function run(): void
    {
        $settings = [
            'max_theory_score' => '14',
            'max_practical_score' => '4',
            'max_ethics_score' => '2',
            'accident_coefficient' => '2',
            'near_miss_coefficient' => '1',
            'warning_coefficient' => '0.5',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
