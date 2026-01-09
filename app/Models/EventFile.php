<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'file_id',
    ];

    // Relasi ke Event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
