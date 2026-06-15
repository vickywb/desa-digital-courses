<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'village_profile_id' => $this->village_profile_id,
            'total_balance' => (float) $this->total_balance,
            'monthly_expense' => (float) $this->monthly_expense,
            'iuran_kebersihan' => (float) $this->iuran_kebersihan,
            'iuran_keamanan' => (float) $this->iuran_keamanan,
            'iuran_makam' => (float) $this->iuran_makam,
            'expense_keamanan' => (float) $this->expense_keamanan,
            'expense_kebersihan' => (float) $this->expense_kebersihan,
        ];
    }
}
