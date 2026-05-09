<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private EventService $eventService
    ){}

    public function index()
    {
        //
    }

    public function store(EventStoreRequest $request)
    {
       $event = $this->eventService->createEvent($request->validated(), $request->file('image') ?? null);
        return ResponseHelper::success('Event created successfully', [
            new EventResource($event)
        ], 201);
    }

    public function show(string $id)
    {
        //
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $this->eventService->updateEvent($request->validated(), $request->file('image') ?? null, $event);
        return ResponseHelper::success('Event updated successfully', [
            new EventResource($event->fresh())
        ], 200);
    }

    public function destroy(Event $event)
    {
        $this->eventService->deleteEvent($event);
        return ResponseHelper::success('Event deleted successfully', null, 200);
    }
}
