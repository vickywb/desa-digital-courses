<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventParticipantStoreRequest;
use App\Http\Requests\EventParticipantUpdateRequest;
use App\Http\Resources\EventParticipantResource;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventParticipantController extends Controller
{
    public function __construct(private FileService $fileService) {}

    public function index(Event $event)
    {
        $participants = $event->eventParticipants()
            ->with(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file'])
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
            ->with(['event.file', 'headOfFamily', 'familyMember', 'members.familyMember', 'file'])
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

        $data = $request->validated();
        $memberIds = $data['member_ids'];
        unset($data['member_ids']);

        $participant = DB::transaction(function () use ($data, $memberIds, $event, $headOfFamily, $request) {
            $fileId = $request->hasFile('proof')
                ? $this->fileService->handleUploadAndSave($request->file('proof'), 'file/event-proof')?->id
                : null;

            $firstFamilyMemberId = null;
            foreach ($memberIds as $mid) {
                if ($mid !== 'head') {
                    $firstFamilyMemberId = $mid;
                    break;
                }
            }

            $totalPrice = (float) $event->price * (int) $data['quantity'];

            $participant = EventParticipant::create([
                'event_id' => $event->id,
                'head_of_family_id' => $headOfFamily->id,
                'family_member_id' => $firstFamilyMemberId,
                'file_id' => $fileId,
                'quantity' => $data['quantity'],
                'total_price' => $totalPrice,
                'payment_status' => $data['payment_status'],
            ]);

            foreach ($memberIds as $mid) {
                $participant->members()->create([
                    'member_type' => $mid === 'head' ? 'head_of_family' : 'family_member',
                    'family_member_id' => $mid === 'head' ? null : $mid,
                ]);
            }

            return $participant;
        });

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

        $participant->fill($request->validated());
        $participant->save();
        $participant->load(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file']);

        return ResponseHelper::success(
            'Event participant updated successfully',
            new EventParticipantResource($participant),
            200
        );
    }

    public function destroy(Event $event, EventParticipant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        DB::transaction(function () use ($participant) {
            $fileId = $participant->file_id;
            $participant->delete();
            if ($fileId) {
                $this->fileService->deleteFile($fileId);
            }
        });

        return ResponseHelper::success(
            'Event participant cancelled successfully',
            new EventParticipantResource($participant),
            200
        );
    }
}
