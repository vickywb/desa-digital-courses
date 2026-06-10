<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\HeadOfFamily;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class HeadOfFamilySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', Role::HeadOfFamily)->get();
        $faker = Factory::create('id_ID');

        foreach ($users as $user) {
            HeadOfFamily::create([
                'user_id' => $user->id,
                'full_name' => $user->username,
                'identity_number' => $faker->nik(),
                'gender' => 'male',
                'date_of_birth' => $faker->date(),
                'phone_number' => $faker->phoneNumber(),
                'occupation' => $faker->jobTitle(),
                'marital_status' => $faker->randomElement(['married']),
            ]);
        }
    }
}
