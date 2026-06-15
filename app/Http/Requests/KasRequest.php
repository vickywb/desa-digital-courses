<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_balance' => 'sometimes|numeric|min:0',
            'monthly_expense' => 'sometimes|numeric|min:0',
            'iuran_kebersihan' => 'sometimes|numeric|min:0',
            'iuran_keamanan' => 'sometimes|numeric|min:0',
            'iuran_makam' => 'sometimes|numeric|min:0',
            'expense_keamanan' => 'sometimes|numeric|min:0',
            'expense_kebersihan' => 'sometimes|numeric|min:0',
        ];
    }
}
