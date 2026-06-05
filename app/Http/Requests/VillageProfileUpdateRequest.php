<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VillageProfileUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'about' => 'sometimes|string',
            'headman' => 'sometimes|string|max:255',
            'people' => 'sometimes|integer|min:0',
            'agriculture_area' => 'sometimes|string|max:255',
            'total_area' => 'sometimes|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
