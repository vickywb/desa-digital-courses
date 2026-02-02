<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'file_id',
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
