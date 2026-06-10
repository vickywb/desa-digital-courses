<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Credentials:
     * - admin     → admin@test.com / admin123
     * - Kades     → kades@test.com  / kades123
     * - Staff 1-3 → staff1-3@test.com / staff123
     * - Keluarga  → {nama}@test.com / warga123
     */
    public function run(): void
    {
        $users = [
            ['username' => 'admin', 'email' => 'admin@test.com', 'password' => Hash::make('admin123'), 'is_active' => true, 'role' => Role::Admin],
            ['username' => 'Kepala Desa', 'email' => 'kades@test.com', 'password' => Hash::make('kades123'), 'is_active' => true, 'role' => Role::HeadVillage],
            ['username' => 'Staff 1', 'email' => 'staff1@test.com', 'password' => Hash::make('staff123'), 'is_active' => true, 'role' => Role::Staff],
            ['username' => 'Staff 2', 'email' => 'staff2@test.com', 'password' => Hash::make('staff123'), 'is_active' => true, 'role' => Role::Staff],
            ['username' => 'Staff 3', 'email' => 'staff3@test.com', 'password' => Hash::make('staff123'), 'is_active' => true, 'role' => Role::Staff],
            ['username' => 'Slamet', 'email' => 'slamet@test.com', 'password' => Hash::make('warga123'), 'is_active' => true, 'role' => Role::HeadOfFamily],
            ['username' => 'Agus', 'email' => 'agus@test.com', 'password' => Hash::make('warga123'), 'is_active' => true, 'role' => Role::HeadOfFamily],
            ['username' => 'Budi', 'email' => 'budi@test.com', 'password' => Hash::make('warga123'), 'is_active' => true, 'role' => Role::HeadOfFamily],
            ['username' => 'Bambang', 'email' => 'bambang@test.com', 'password' => Hash::make('warga123'), 'is_active' => true, 'role' => Role::HeadOfFamily],
            ['username' => 'Adi', 'email' => 'adi@test.com', 'password' => Hash::make('warga123'), 'is_active' => true, 'role' => Role::HeadOfFamily],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
