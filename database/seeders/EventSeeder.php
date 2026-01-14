<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'thumbnail' => '',
                'title' => 'Community Clean-Up Day',
                'description' => 'Join us for a day of cleaning and beautifying our neighborhood park.',
                'price' => 20000,
                'start_date' => now(),
                'is_active' => true,
            ],
            [
                'thumbnail' => '',
                'title' => 'Health Awareness Workshop',
                'description' => 'A workshop focused on promoting healthy living and wellness.',
                'price' => 15000,
                'start_date' => now(),
                'is_active' => true,
            ],
            [
                'thumbnail' => '',
                'title' => 'Cultural Festival',
                'description' => 'Celebrate our diverse cultures with food, music, and dance.',
                'price' => 10000,
                'start_date' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
