<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeadOfFamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'identity_number' => $this->identity_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'occupation' => $this->occupation,
            'marital_status' => $this->marital_status,
            'phone_number' => $this->phone_number,
            'family_members_count' => $this->whenCounted('familyMembers'),
            'profile_picture' => $this->whenLoaded('file', fn () => $this->file ? new FileResource($this->file) : null),
        ];
    }
}
