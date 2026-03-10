<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VillageProfileStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'about' => 'required|string',
            'headman' => 'required|string|max:255',
            'people' => 'required|integer|min:0',
            'agriculture_area' => 'required|string|max:255',
            'total_area' => 'required|string|max:255',
        ];
    }
}
