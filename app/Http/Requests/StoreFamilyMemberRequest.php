<?php

namespace App\Http\Requests;

use App\Enums\FamilyRelation;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('family_members', 'email')],
            'identity_number' => ['required', 'string', 'max:50', Rule::unique('family_members', 'identity_number')],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'occupation' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'marital_status' => ['required', Rule::enum(MaritalStatus::class)],
            'relation' => ['required', Rule::enum(FamilyRelation::class)],
        ];
    }
}
