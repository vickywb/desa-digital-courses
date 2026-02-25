<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use App\Models\EventParticipant;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EventParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $headOfFamilies = HeadOfFamily::all();
        $familyMember = FamilyMember::all();

        foreach ($events as $event) {
            foreach ($headOfFamilies as $headOfFamily) {
                EventParticipant::create([
                    'event_id' => $event->id,
                    'head_of_family_id' => $headOfFamily->id,
                    'family_member_id' => $familyMember->random()->id,
                    'quantity' => 2,
                    'total_price' => 20000,
                    'payment_status' => 'paid',
                ]);
            }
        }
    }
}
