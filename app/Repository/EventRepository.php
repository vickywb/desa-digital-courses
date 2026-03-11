<?php

namespace App\Repository;

use App\Models\Event;

class EventRepository
{
    public function __construct(private Event $event) {}

    public function save(Event $event): Event
    {
        $event->save();
        return $event->fresh();
    }
}