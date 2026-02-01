<?php

namespace Database\Seeders;

use App\Models\HeadOfFamily;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeadOfFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::skip(1)->take(4)->get();
        $faker = Factory::create('id_ID'); // Lokalisasi Indonesia
        foreach ($users as $user) {

            HeadOfFamily::create([
                'user_id' => $user->id,
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
