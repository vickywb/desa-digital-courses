<?php

namespace Database\Seeders;

use App\Models\Development;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developments = [
            [
                'thumbnail' => '',
                'title' => 'Development 1',
                'description' => 'Description for Development 1',
                'person_in_charge' => 'Person In Charge 1',
                'amount' => 100000,
                'start_date' => now(),
                'end_date' => now()->addDays(20),
                'status' => 'ongoing'
            ],
            [
                'thumbnail' => '',
                'title' => 'Development 2',
                'description' => 'Description for Development 2',
                'person_in_charge' => 'Person In Charge 2',
                'amount' => 200000,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'status' => 'completed'
            ],
            [
                'thumbnail' => '',
                'title' => 'Development 3',
                'description' => 'Description for Development 3',
                'person_in_charge' => '',
                'amount' => 50000,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'status' => 'planned',
            ],
            [
                'thumbnail' => '',
                'title' => 'Development 4',
                'description' => 'Description for Development 4',
                'person_in_charge' => '',
                'amount' => 60000,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'status' => 'planned',
            ]
        ];

        foreach ($developments as $development) {
            Development::create($development);
        }
    }
}
