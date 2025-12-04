<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Maak voor iedere rol twee gebruikersaccounts inclusief rollen.
     */
    public function run(): void
    {
        $roles = [
            'patient',
            'mondhygienist',
            'assistent',
            'praktijkmanagement',
        ];

        foreach ($roles as $role) {
            foreach (range(1, 2) as $index) {
                $name = ucfirst($role) . ' ' . $index;
                $email = sprintf('%s%d@smilepro.test', $role, $index);

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('password'),
                    ]
                );

                $user->syncRoles([$role]);
            }
        }
    }
}
