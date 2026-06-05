<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventParticipantStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $headOfFamilyId = optional($this->user()?->headOfFamily)->id;

        return [
            'family_member_id' => [
                'nullable',
                'uuid',
                Rule::exists('family_members', 'id')
                    ->where(fn ($query) => $query->where('head_of_family_id', $headOfFamilyId)),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in(['free', 'pending', 'paid'])],
        ];
    }
}
