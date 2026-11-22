<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PersonnelRoleSeeder::class,
            StationSeeder::class,
            SettingsSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'vahidmehdipour@gmail.com'],
            [
                'name' => 'وحید مهدیپور',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super-admin');
    }
}
