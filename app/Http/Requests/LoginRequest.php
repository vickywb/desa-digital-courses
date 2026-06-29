<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required_without_all:username,identity_number|string|email',
            'username' => 'required_without_all:email,identity_number|string|min:3',
            'identity_number' => 'required_without_all:email,username|string|max:50',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without_all' => 'The email, username, or identity number field is required.',
            'username.required_without_all' => 'The email, username, or identity number field is required.',
            'identity_number.required_without_all' => 'The email, username, or identity number field is required.',
        ];
    }
}
