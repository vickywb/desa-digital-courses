<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventParticipantStoreRequest;
use App\Http\Requests\EventParticipantUpdateRequest;
use App\Http\Resources\EventParticipantResource;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;

class EventParticipantController extends Controller
{
    public function index(Event $event)
    {
        $participants = $event->eventParticipants()
            ->with(['event', 'headOfFamily', 'familyMember'])
            ->get();

        return ResponseHelper::success(
            'Event participants retrieved successfully',
            EventParticipantResource::collection($participants),
            200
        );
    }

    public function myParticipations(Request $request)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        $participants = EventParticipant::query()
            ->where('head_of_family_id', $headOfFamily->id)
            ->with(['event.file', 'headOfFamily', 'familyMember'])
            ->latest()
            ->get();

        return ResponseHelper::success(
            'My event participants retrieved successfully',
            EventParticipantResource::collection($participants),
            200
        );
    }

    public function store(EventParticipantStoreRequest $request, Event $event)
    {
        $headOfFamily = $request->user()->headOfFamily;
        abort_unless($headOfFamily, 403);

        $participant = EventParticipant::create([
            'event_id' => $event->id,
            'head_of_family_id' => $headOfFamily->id,
            ...$request->validated(),
        ]);

        $participant->load(['event', 'headOfFamily', 'familyMember']);

        return ResponseHelper::success(
            'Event participant created successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function show(Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant->load(['event', 'headOfFamily', 'familyMember']);

        return ResponseHelper::success(
            'Event participant retrieved successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function update(EventParticipantUpdateRequest $request, Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant->fill($request->validated());
        $participant->save();
        $participant->load(['event', 'headOfFamily', 'familyMember']);

        return ResponseHelper::success(
            'Event participant updated successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function destroy(Request $request, Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);
        abort_unless($participant->head_of_family_id === $request->user()->headOfFamily?->id, 403);

        $participant->delete();

        return ResponseHelper::success(
            'Event participant cancelled successfully',
            new EventParticipantResource($participant),
            200
        );
    }
}
