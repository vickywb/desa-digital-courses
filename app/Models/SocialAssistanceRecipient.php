<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistanceRecipient extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'social_assistance_id',
        'head_of_family_id',
        'file_id',
        'bank',
        'amount',
        'account_number',
        'reason',
        'status',
    ];

    public function socialAssistance(): BelongsTo
    {
        return $this->belongsTo(SocialAssistance::class);
    }

    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
