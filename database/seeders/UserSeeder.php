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
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::Admin
            ],
            [
                'username' => 'Slamet',
                'email' => 'slamet@test.com',
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::HeadVillage
            ],
            [
                'username' => 'Agus',
                'email' => 'agus@test.com',
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::HeadOfFamily
            ],
            [
                'username' => 'Budi',
                'email' => 'budi@test.com',
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::HeadOfFamily
            ],
            [
                'username' => 'Bambang',
                'email' => 'bambang@test.com',
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::HeadOfFamily
            ],
            [
                'username' => 'Adi',
                'email' => 'adi@test.com',
                'password' => Hash::make('secret123'),
                'is_active' => true,
                'role' => Role::Staff
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
