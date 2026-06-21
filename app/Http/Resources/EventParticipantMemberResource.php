<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventParticipantMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_type' => $this->member_type,
            'family_member' => new FamilyMemberResource($this->whenLoaded('familyMember')),
        ];
    }
}
