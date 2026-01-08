<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentApplicant extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'development_id',
        'user_id',
        'status',
    ];

    // Relasi dengan model Development
    public function development(): BelongsTo
    {
        return $this->belongsTo(Development::class);
    }

    // Relasi dengan model User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
