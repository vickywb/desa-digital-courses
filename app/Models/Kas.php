<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kas extends Model
{
    use HasUuids;

    protected $fillable = [
        'village_profile_id',
        'total_balance',
        'monthly_expense',
        'iuran_kebersihan',
        'iuran_keamanan',
        'iuran_makam',
        'expense_keamanan',
        'expense_kebersihan',
    ];

    protected function casts(): array
    {
        return [
            'total_balance' => 'decimal:2',
            'monthly_expense' => 'decimal:2',
            'iuran_kebersihan' => 'decimal:2',
            'iuran_keamanan' => 'decimal:2',
            'iuran_makam' => 'decimal:2',
            'expense_keamanan' => 'decimal:2',
            'expense_kebersihan' => 'decimal:2',
        ];
    }

    public function villageProfile(): BelongsTo
    {
        return $this->belongsTo(VillageProfile::class);
    }
}
