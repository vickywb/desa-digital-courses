<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'file_id',
        'title',
        'description',
        'price',
        'start_date',
        'is_active',
    ];

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    // Relasi ke EventParticipant
    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    // Tambahkan casting untuk tipe data
    protected $casts = [
        'price' => 'string',
        'start_date' => 'datetime',
        'is_active' => 'boolean',
    ];
}
