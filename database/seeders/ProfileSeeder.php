<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'thumbnail' => '',
                'name' => 'Pondok Nirwana Anggaswangi',
                'about' => 'Pondok Nirwana Anggaswangi adalah desa yang berkomitmen pada pengembangan digital.',
                'headman' => 'Budi Santoso',
                'people' => 1200,
                'agriculture_area' => 500,
                'total_area' => 1000,
            ]
        ];

        foreach ($profiles as $profile) {
            Profile::create($profile);
        }
    }
}
