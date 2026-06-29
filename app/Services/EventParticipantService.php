<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\HeadOfFamily;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EventParticipantService
{
    public function __construct(
        private FileService $fileService,
    ) {}

    public function getParticipantByEvent(Event $event): mixed
    {
        return $event->eventParticipants()
            ->with(['event', 'headOfFamily', 'familyMember', 'members.familyMember', 'file'])
            ->paginate(20);
    }

    public function getMyParticipations(HeadOfFamily $headOfFamily): mixed
    {
        return EventParticipant::query()
            ->where('head_of_family_id', $headOfFamily->id)
            ->with(['event.file', 'headOfFamily', 'familyMember', 'members.familyMember', 'file'])
            ->latest()
            ->paginate(20);
    }

    public function store(Event $event, HeadOfFamily $headOfFamily, array $data, array $memberIds, ?UploadedFile $proof): EventParticipant
    {
        return DB::transaction(function () use ($data, $memberIds, $event, $headOfFamily, $proof) {
            $fileId = $proof
                ? $this->fileService->handleUploadAndSave($proof, 'file/event-proof')?->id
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

            $participant->members()->createMany(
                collect($memberIds)->map(fn ($mid) => [
                    'member_type' => $mid === 'head' ? 'head_of_family' : 'family_member',
                    'family_member_id' => $mid === 'head' ? null : $mid,
                ])->all()
            );

            return $participant;
        });
    }

    public function update(array $data, EventParticipant $participant): EventParticipant
    {
        $participant->fill($data);
        $participant->save();

        return $participant;
    }

    public function destroy(EventParticipant $participant): void
    {
        DB::transaction(function () use ($participant) {
            $fileId = $participant->file_id;
            $participant->delete();
            if ($fileId) {
                $this->fileService->deleteFile($fileId);
            }
        });
    }
}
