<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadOfFamily extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'user_id',
        'profile_picture',
        'identify_number',
        'gender',
        'date_birth',
        'phone_number',
        'occupation',
        'marital_status',
    ];

    protected $casts = [
        'date_birth' => 'date',
    ];

    // Relasi dengan model User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
