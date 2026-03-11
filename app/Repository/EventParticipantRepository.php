<?php

namespace App\Repository;

use App\Models\EventParticipant;

class EventParticipantRepository
{
    public function __construct(private EventParticipant $eventParticipant) {}

    public function save(EventParticipant $eventParticipant)
    {
        $eventParticipant->save();
        return $eventParticipant;
    }
}