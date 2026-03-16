<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageProfileFile extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'village_profile_id',
        'file_id',
    ];

    // Relasi dengan model Profile
    public function villageProfile(): BelongsTo
    {
        return $this->belongsTo(VillageProfile::class);
    }

    // Relasi dengan model File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
