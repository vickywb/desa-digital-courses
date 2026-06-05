<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventParticipantUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'family_member_id' => ['nullable', 'uuid', Rule::exists('family_members', 'id')],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', Rule::in(['free', 'pending', 'paid'])],
        ];
    }
}
