<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevelopmentUpdateRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'person_in_charge' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric',
            'start_date' => 'sometimes|date_format:Y-m-d H:i:s',
            'end_date' => 'sometimes|date_format:Y-m-d H:i:s|after_or_equal:start_date',
            'status' => 'sometimes|in:planned,ongoing,completed',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:png,jpg,webp|max:2048',
        ];
    }
}
