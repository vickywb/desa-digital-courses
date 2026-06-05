<?php

namespace App\Models;

use App\Enums\FamilyRelation;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMember extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'head_of_family_id',
        'file_id',
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
        'gender' => Gender::class,
        'marital_status' => MaritalStatus::class,
        'relation' => FamilyRelation::class,
    ];

    // Relasi dengan model HeadOfFamily
    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }

    // Relasi ke file profil anggota keluarga
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
