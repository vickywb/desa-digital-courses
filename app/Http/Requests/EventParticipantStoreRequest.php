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

    protected function prepareForValidation(): void
    {
        if ($this->has('member_ids') && is_string($this->member_ids)) {
            $this->merge([
                'member_ids' => json_decode($this->member_ids, true),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_status' => ['required', Rule::in(['free', 'pending', 'paid'])],
            'proof' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }
}
