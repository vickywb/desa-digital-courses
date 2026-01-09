<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAssistanceFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'social_assistance_id',
        'file_id',
    ];

    // Relasi ke SocialAssistance
    public function socialAssistance(): BelongsTo
    {
        return $this->belongsTo(SocialAssistance::class);
    }

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
