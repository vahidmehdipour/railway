<?php

namespace Database\Seeders;

use App\Models\EvaluationPeriod;
use Illuminate\Database\Seeder;

class EvaluationPeriodSeeder extends Seeder
{
    /**
     * Seed the default evaluation period.
     */
    public function run(): void
    {
        EvaluationPeriod::updateOrCreate(
            ['title' => 'دوره اول - 1404'],
            [
                'start_date' => '2025-03-21',
                'end_date' => '2026-03-20',
                'is_active' => true,
            ],
        );
    }
}
