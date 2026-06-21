<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => new EventResource($this->whenLoaded('event')),
            'head_of_family' => new HeadOfFamilyResource($this->whenLoaded('headOfFamily')),
            'family_member' => new FamilyMemberResource($this->whenLoaded('familyMember')),
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'members' => EventParticipantMemberResource::collection($this->whenLoaded('members')),
            'proof' => new FileResource($this->whenLoaded('file')),
        ];
    }
}
