<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'praktijkmanagement', 'guard_name' => 'web'],
            ['name' => 'praktijkmanagement', 'guard_name' => 'web']
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@smilepro.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
            ]
        );

        $admin->syncRoles([$role->name]);
    }
}
