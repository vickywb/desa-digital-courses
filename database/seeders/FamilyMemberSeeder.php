<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FamilyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headOfFamilies = HeadOfFamily::all();
        $faker = Factory::create('id_ID'); // Lokalisasi Indonesia
        $wifeNames = ['Siti Aminah', 'Anggita Rahma', 'Marikah Dewi', 'Azahra Putri', 'Lina Marlina', 'Dewi Sartika', 'Rina Susanti', 'Maya Sari', 'Fitri Handayani', 'Yulia Anggraini'];

        foreach ($headOfFamilies as $headOfFamily) {
            $wifeName = $wifeNames[array_rand($wifeNames)];

            FamilyMember::create([
                'head_of_family_id' => $headOfFamily->id,
                'full_name'         => $wifeName,
                'email'             => $faker->unique()->safeEmail(),
                'identity_number'   => $faker->nik(),
                'phone_number'      => '08' . $faker->numerify('##########'),
                'occupation'        => $faker->jobTitle(),
                'gender'            => $faker->randomElement(['female']),
                'relation'          => $faker->randomElement(['wife']),
                'date_of_birth'     => $faker->date('Y-m-d', '-20 years'),
                'marital_status'    => $faker->randomElement(['married']),
            ]);
        }

    }
}
