<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;

class EventController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function index()
    {
        $events = Event::with('file')->get();

        return ResponseHelper::success(
            'Events retrieved successfully',
            EventResource::collection($events),
            200
        );
    }

    public function store(EventStoreRequest $request)
    {
        $event = $this->eventService->createEvent($request->validated(), $request->file('image') ?? null);

        return ResponseHelper::success('Event created successfully', [
            new EventResource($event),
        ], 201);
    }

    public function show(Event $event)
    {
        $event->load('file');

        return ResponseHelper::success(
            'Event retrieved successfully',
            new EventResource($event),
            200
        );
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $this->eventService->updateEvent($request->validated(), $request->file('image') ?? null, $event);

        return ResponseHelper::success('Event updated successfully', [
            new EventResource($event->fresh()),
        ], 200);
    }

    public function destroy(Event $event)
    {
        $this->eventService->deleteEvent($event);

        return ResponseHelper::success('Event deleted successfully', null, 200);
    }
}
