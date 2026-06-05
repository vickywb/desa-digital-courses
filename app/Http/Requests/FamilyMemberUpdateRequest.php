<?php

namespace App\Http\Requests;

use App\Enums\FamilyRelation;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FamilyMemberUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('family_members', 'email')->ignore($this->route('member'))],
            'identity_number' => ['nullable', 'string', 'max:50', Rule::unique('family_members', 'identity_number')->ignore($this->route('member'))],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'marital_status' => ['nullable', Rule::enum(MaritalStatus::class)],
            'relation' => ['nullable', Rule::enum(FamilyRelation::class)],
        ];
    }
}
