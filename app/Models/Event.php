<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'thumbnail',
        'title',
        'description',
        'price',
        'start_date',
        'is_active',
    ];

    // Tambahkan casting untuk tipe data
    protected $casts = [
        'price' => 'string',
        'start_date' => 'datetime',
        'is_active' => 'boolean',
    ];
}
