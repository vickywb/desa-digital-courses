<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
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
            'email' => $this->email,
            'identity_number' => $this->identity_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth->format('d-m-Y H:i:s'),
            'phone_number' => $this->phone_number,
            'occupation' => $this->occupation,
            'marital_status' => $this->marital_status,
            'relation' => $this->relation,
            'family_member_file' => new FileResource($this->whenLoaded('file')),
            'head_of_family' => new HeadOfFamilyResource($this->whenLoaded('headOfFamily')),
        ];
    }
}
