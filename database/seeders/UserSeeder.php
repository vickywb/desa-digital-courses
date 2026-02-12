<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('secret'),
                'role' => Role::Admin
            ],
            [
                'username' => 'kades',
                'email' => 'kades@test.com',
                'password' => Hash::make('secret'),
                'role' => Role::HeadVillage
            ],
            [
                'username' => 'agus',
                'email' => 'agus@test.com',
                'password' => Hash::make('secret'),
                'role' => Role::HeadOfFamily
            ],
            [
                'username' => 'budi',
                'email' => 'budi@test.com',
                'password' => Hash::make('secret'),
                'role' => Role::HeadOfFamily
            ],
            [
                'username' => 'bambang',
                'email' => 'bambang@test.com',
                'password' => Hash::make('secret'),
                'role' => Role::HeadOfFamily
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
