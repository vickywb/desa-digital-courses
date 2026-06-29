<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventParticipantStoreRequest;
use App\Http\Requests\EventParticipantUpdateRequest;
use App\Http\Resources\EventParticipantResource;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Services\EventParticipantService;
use Illuminate\Http\Request;

class EventParticipantController extends Controller
{
    public function __construct(
        private EventParticipantService $eventParticipantService,
    ) {}

    public function index(Event $event)
    {
        $participants = $this->eventParticipantService->getParticipantByEvent($event);

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

        $participants = $this->eventParticipantService->getMyParticipations($headOfFamily);

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

        $data = $request->validated();
        $memberIds = $data['member_ids'];
        unset($data['member_ids']);

        // Validasi bahwa semua member_ids milik keluarga yang sedang login
        $familyMemberIds = $headOfFamily->familyMembers()->pluck('id')->toArray();
        foreach ($memberIds as $mid) {
            if ($mid !== 'head' && ! in_array($mid, $familyMemberIds)) {
                abort(422, 'Salah satu anggota keluarga tidak valid.');
            }
        }

        $participant = $this->eventParticipantService->store(
            $event,
            $headOfFamily,
            $data,
            $memberIds,
            $request->hasFile('proof') ? $request->file('proof') : null,
        );

        $participant->load(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file']);

        return ResponseHelper::success(
            'Event participant created successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function show(Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant->load(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file']);

        return ResponseHelper::success(
            'Event participant retrieved successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function update(EventParticipantUpdateRequest $request, Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant = $this->eventParticipantService->update($request->validated(), $participant);
        $participant->load(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file']);

        return ResponseHelper::success(
            'Event participant updated successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function destroy(Request $request, Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        // Jika diakses oleh head_of_family, pastikan miliknya sendiri
        $headOfFamily = $request->user()->headOfFamily;
        if ($headOfFamily) {
            abort_unless($participant->head_of_family_id === $headOfFamily->id, 403);
        }

        $this->eventParticipantService->destroy($participant);

        return ResponseHelper::success(
            'Event participant cancelled successfully',
            new EventParticipantResource($participant),
            200
        );
    }
}
