<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class PersonnelRoleSeeder extends Seeder
{
    /**
     * Seed the job roles required by the personnel form.
     */
    public function run(): void
    {
        $roles = [
            ['name_en' => 'Train Chief', 'name_fa' => 'رئیس قطار'],
            ['name_en' => 'Head Shunter', 'name_fa' => 'سرجابجاشو'],
            ['name_en' => 'Shunter', 'name_fa' => 'جابجاشو'],
            ['name_en' => 'Switchman', 'name_fa' => 'سوزنبان'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name_en' => $role['name_en']],
                ['name_fa' => $role['name_fa']],
            );
        }
    }
}
