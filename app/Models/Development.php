<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Development extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'thumbnail',
        'title',
        'description',
        'person_in_charge',
        'amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'string',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relasi ke DevelopmentApplicant
    public function applicants(): HasMany
    {
        return $this->hasMany(DevelopmentApplicant::class);
    }
}
