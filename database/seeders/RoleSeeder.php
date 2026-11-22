<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()['cache']->forget('spatie.permission.cache');

        foreach (Permissions::all() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $viewer = Role::findOrCreate('viewer', 'web');
        $editor = Role::findOrCreate('editor', 'web');
        $superAdmin = Role::findOrCreate('super-admin', 'web');

        $viewer->syncPermissions(Permissions::viewer());
        $editor->syncPermissions(array_merge(Permissions::viewer(), Permissions::editor()));
        $superAdmin->givePermissionTo(Permission::all());

        $admin = User::updateOrCreate(
            ['email' => 'vahidmehdipour@gmail.com'],
            [
                'name' => 'وحید مهدیپور',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super-admin');

        app()['cache']->forget('spatie.permission.cache');
    }
}
