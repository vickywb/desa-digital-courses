<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileFile extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'profile_id',
        'file_id',
    ];

    // Relasi dengan model Profile
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // Relasi dengan model File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
