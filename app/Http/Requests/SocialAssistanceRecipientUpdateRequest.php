<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialAssistanceRecipientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank' => ['nullable', Rule::in(['BCA', 'Mandiri', 'BNI', 'BRI'])],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'account_number' => ['nullable', 'string', 'max:255', Rule::unique('social_assistance_recipients', 'account_number')->ignore($this->route('recipient'))],
            'reason' => ['nullable', 'string'],
            'proof' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }
}
