<?php

namespace App\Repository;

use App\Models\Event;

class EventRepository
{
    private $event;

    public function __construct(Event $event) {
        $this->event = $event;
    }

    public function save(Event $event)
    {
        $event->save();

        return $event;
    }
}