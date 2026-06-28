<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventParticipantMember;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use Illuminate\Database\Seeder;

class EventParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();
        $headOfFamilies = HeadOfFamily::all();

        foreach ($events as $event) {
            foreach ($headOfFamilies as $headOfFamily) {
                $familyMembers = FamilyMember::where('head_of_family_id', $headOfFamily->id)->get();

                $quantity = $familyMembers->isNotEmpty() ? $familyMembers->count() + 1 : 1;

                $participant = EventParticipant::create([
                    'event_id' => $event->id,
                    'head_of_family_id' => $headOfFamily->id,
                    'family_member_id' => $familyMembers->isNotEmpty() ? $familyMembers->random()->id : null,
                    'quantity' => $quantity,
                    'total_price' => (float) $event->price * $quantity,
                    'payment_status' => 'paid',
                    'file_id' => null,
                ]);

                EventParticipantMember::create([
                    'event_participant_id' => $participant->id,
                    'member_type' => 'head_of_family',
                    'family_member_id' => null,
                ]);

                foreach ($familyMembers as $member) {
                    EventParticipantMember::create([
                        'event_participant_id' => $participant->id,
                        'member_type' => 'family_member',
                        'family_member_id' => $member->id,
                    ]);
                }
            }
        }
    }
}
