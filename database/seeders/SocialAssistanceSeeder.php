<?php

namespace Database\Seeders;

use App\Models\SocialAssistance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialAssistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialAssistances = [
            [
                'title' => 'Bantuan Sosial 1',
                'category' => 'healthcare',
                'amount' => 500000,
                'provider' => 'Pemerintah',
                'description' => 'Bantuan sosial untuk keluarga miskin.',
                'is_available' => true,
            ],
            [
                'title' => 'Bantuan Sosial 2',
                'category' => 'cash',
                'amount' => 300000,
                'provider' => 'Pemerintah',
                'description' => 'Bantuan sosial untuk anak yatim.',
                'is_available' => true,
            ],
            [
                'title' => 'Bantuan Sosial 3',
                'category' => 'healthcare',
                'amount' => 400000,
                'provider' => 'Pemerintah',
                'description' => 'Bantuan sosial untuk lansia.',
                'is_available' => false,
            ]
        ];

        foreach ($socialAssistances as $socialAssistance) {
            SocialAssistance::create($socialAssistance);
        }
    }
}
