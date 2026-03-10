<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
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
            'username'        => 'required|string|min:3|max:24|unique:users,username|alpha_dash',
            'full_name'       => 'required|string|min:3|max:50',
            'email'           => 'required|string|email|max:255|unique:users,email',
            'password'        => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'identity_number' => 'required|string|max:20|unique:head_of_families,identity_number',
            'gender'          => 'required|in:male,female',
            'date_of_birth'   => 'required|date|before:today|after:1900-01-01',
            'occupation'      => 'required|string|max:100',
            'marital_status'  => 'required|in:single,married,widower,widow',
            'phone_number'    => 'nullable|string|regex:/^[0-9+\-\s()]+$/|min:10|max:15|unique:head_of_families,phone_number',
        ];
    }
}
