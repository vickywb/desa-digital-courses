<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HeadOfFamilyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:50', Rule::unique('head_of_families', 'identity_number')->ignore($this->route('head_family'))],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'date_of_birth' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', Rule::enum(MaritalStatus::class)],
        ];
    }
}
