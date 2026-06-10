<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHeadOfFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:50', Rule::unique('head_of_families', 'identity_number')],
            'gender' => ['required', Rule::enum(Gender::class)],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'occupation' => ['required', 'string', 'max:255'],
            'marital_status' => ['required', Rule::enum(MaritalStatus::class)],
        ];
    }
}
