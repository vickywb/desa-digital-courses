<?php

namespace Database\Seeders;

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
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('secret'),
                'role' => 'admin'
            ],
            [
                'name' => 'Kades',
                'email' => 'kades@test.com',
                'password' => Hash::make('secret'),
                'role' => 'head_village'
            ],
            [
                'name' => 'Agus',
                'email' => 'agus@test.com',
                'password' => Hash::make('secret'),
                'role' => 'head_of_family'
            ],
            [
                'name' => 'Budi',
                'email' => 'budi@test.com',
                'password' => Hash::make('secret'),
                'role' => 'head_of_family'
            ],
            [
                'name' => 'Bambang',
                'email' => 'bambang@test.com',
                'password' => Hash::make('secret'),
                'role' => 'head_of_family'
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
