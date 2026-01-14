<?php

namespace App\Repository;

use App\Models\EventParticipant;

class EventParticipantRepository
{
    private $eventParticipant;

    public function __construct(EventParticipant $eventParticipant) {
        $this->eventParticipant = $eventParticipant;
    }

    public function save(EventParticipant $eventParticipant)
    {
        $eventParticipant->save();

        return $eventParticipant;
    }
}