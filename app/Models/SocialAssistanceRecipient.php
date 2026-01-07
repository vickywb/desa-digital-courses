<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistanceRecipient extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'social_assistance_id',
        'head_of_family_id',
        'bank',
        'amount',
        'account_number',
        'reason',
        'proof',
        'status',
    ];

    // Relasi ke SocialAssistance
    public function socialAssistance(): BelongsTo
    {
        return $this->belongsTo(SocialAssistance::class);
    }

    // Relasi ke HeadOfFamily
    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }
}
