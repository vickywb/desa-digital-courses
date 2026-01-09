<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'development_id',
        'file_id',
    ];

    // Relasi ke Development
    public function development(): BelongsTo
    {
        return $this->belongsTo(Development::class);
    }

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
