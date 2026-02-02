<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistance extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'file_id',
        'title',
        'category',
        'amount',
        'provider',
        'description',
        'is_available',
    ];

    protected $casts = [
        'amount' => 'string',
        'is_available' => 'boolean',
    ];

    // Relasi ke SocialAssistanceRecipient
    public function recipients(): HasMany
    {
        return $this->hasMany(SocialAssistanceRecipient::class);
    }
}
