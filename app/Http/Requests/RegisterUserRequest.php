<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'        => 'required|string|max:24',
            'email'           => 'required|string|email|unique:users,email',
            'identity_number' => 'required|numeric|unique:head_of_families,identity_number',
            'gender'          => 'required|in:male,female',
            'date_of_birth'   => 'required|date|before:today',
            'occupation'      => 'required|string|max:255',
            'marital_status'  => 'required|in:single,married,widower,widow',
            'phone_number' => 'required|numeric|unique:head_of_families,phone_number',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ];
    }
}
