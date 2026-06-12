<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialAssistanceRecipientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank' => ['required', Rule::in(['BCA', 'Mandiri', 'BNI', 'BRI'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'account_number' => ['required', 'string', 'max:255', Rule::unique('social_assistance_recipients', 'account_number')],
            'reason' => ['required', 'string'],
            'proof' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
