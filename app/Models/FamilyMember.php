<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMember extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'head_of_family_id',
        'full_name',
        'email',
        'date_of_birth',
        'occupation',
        'phone_number',
        'identity_number',
        'profile_picture',
        'gender',
        'marital_status',
        'relation',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relasi dengan model HeadOfFamily
    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }
}
