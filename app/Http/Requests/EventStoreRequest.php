<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'start_date' => 'required|date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:png,jpg,webp|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('start_date')) {
            $this->merge([
                'start_date' => str_replace('T', ' ', $this->start_date),
            ]);
        }
    }
}
