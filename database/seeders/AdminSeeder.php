<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smilepro.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
            ]
        );

        $admin->syncRoles(['praktijkmanagement']);
    }
}
