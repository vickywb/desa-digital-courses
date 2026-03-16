<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceRecipientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bank' => $this->bank,
            'amount' => $this->amount,
            'account_number' => $this->account_number,
            'reason' => $this->reason,
            'proof' => $this->proof,
            'status' => $this->status,
            'social_assistance' => new SocialAssistanceResource($this->whenLoaded('socialAssistance')),
            'head_of_family' => new HeadOfFamilyResource($this->whenLoaded('headOfFamily'))
        ];
    }
}
