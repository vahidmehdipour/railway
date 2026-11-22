<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Personnel;
use App\Models\Role;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed a small set of sample personnel and evaluations.
     */
    public function run(): void
    {
        $roles = Role::pluck('id', 'name_en');
        $period = EvaluationPeriod::where('title', 'دوره اول - 1404')->first();
        $admin = User::where('email', 'admin@example.com')->first();
        $stationId = Station::query()->value('id');

        if (! $period || $roles->isEmpty()) {
            return;
        }

        $personnels = [
            ['first_name' => 'علی', 'last_name' => 'رضایی', 'role' => 'Train Chief', 'entry_year' => 1395, 'national_code' => '0012345678', 'is_active' => true],
            ['first_name' => 'محمد', 'last_name' => 'حسینی', 'role' => 'Head Shunter', 'entry_year' => 1397, 'national_code' => '0023456789', 'is_active' => true],
            ['first_name' => 'رضا', 'last_name' => 'کریمی', 'role' => 'Shunter', 'entry_year' => 1399, 'national_code' => '0034567890', 'is_active' => true],
            ['first_name' => 'حسین', 'last_name' => 'موسوی', 'role' => 'Switchman', 'entry_year' => 1400, 'national_code' => '0045678901', 'is_active' => true],
            ['first_name' => 'مهدی', 'last_name' => 'احمدی', 'role' => 'Train Chief', 'entry_year' => 1392, 'national_code' => '0056789012', 'is_active' => true],
            ['first_name' => 'سعید', 'last_name' => 'نوری', 'role' => 'Shunter', 'entry_year' => 1401, 'national_code' => '0067890123', 'is_active' => true],
            ['first_name' => 'امیر', 'last_name' => 'قاسمی', 'role' => 'Head Shunter', 'entry_year' => 1398, 'national_code' => '0078901234', 'is_active' => true],
            ['first_name' => 'فرهاد', 'last_name' => 'صادقی', 'role' => 'Switchman', 'entry_year' => 1402, 'national_code' => '0089012345', 'is_active' => true],
            ['first_name' => 'جواد', 'last_name' => 'اکبری', 'role' => 'Shunter', 'entry_year' => 1396, 'national_code' => '0090123456', 'is_active' => true],
            ['first_name' => 'یاسر', 'last_name' => 'شریفی', 'role' => 'Train Chief', 'entry_year' => 1394, 'national_code' => '0101234567', 'is_active' => false],
        ];

        $scores = [
            ['theory' => 92, 'practical' => 95, 'ethics' => 90, 'accidents' => 0, 'near_misses' => 1, 'warnings' => 0],
            ['theory' => 85, 'practical' => 88, 'ethics' => 91, 'accidents' => 0, 'near_misses' => 0, 'warnings' => 1],
            ['theory' => 78, 'practical' => 82, 'ethics' => 80, 'accidents' => 1, 'near_misses' => 0, 'warnings' => 2],
            ['theory' => 70, 'practical' => 75, 'ethics' => 84, 'accidents' => 0, 'near_misses' => 2, 'warnings' => 1],
            ['theory' => 96, 'practical' => 94, 'ethics' => 95, 'accidents' => 0, 'near_misses' => 0, 'warnings' => 0],
            ['theory' => 65, 'practical' => 72, 'ethics' => 70, 'accidents' => 1, 'near_misses' => 1, 'warnings' => 3],
            ['theory' => 88, 'practical' => 86, 'ethics' => 89, 'accidents' => 0, 'near_misses' => 1, 'warnings' => 0],
            ['theory' => 74, 'practical' => 79, 'ethics' => 76, 'accidents' => 0, 'near_misses' => 0, 'warnings' => 2],
        ];

        foreach ($personnels as $index => $data) {
            $personnel = Personnel::updateOrCreate(
                ['national_code' => $data['national_code']],
                [
                    'role_id' => $roles[$data['role']],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'entry_year' => $data['entry_year'],
                    'is_active' => $data['is_active'],
                ],
            );

            if (! isset($scores[$index])) {
                continue;
            }

            Evaluation::firstOrCreate(
                [
                    'personnel_id' => $personnel->id,
                    'period_id' => $period->id,
                ],
                [
                    'station_id' => $stationId,
                    'evaluation_date' => now()->toDateString(),
                    'theory_score' => $scores[$index]['theory'],
                    'practical_score' => $scores[$index]['practical'],
                    'ethics_score' => $scores[$index]['ethics'],
                    'accident_count' => $scores[$index]['accidents'],
                    'near_miss_count' => $scores[$index]['near_misses'],
                    'warning_count' => $scores[$index]['warnings'],
                    'user_id' => $admin?->id,
                ],
            );
        }
    }
}
