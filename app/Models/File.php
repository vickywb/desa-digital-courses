<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class File extends Model
{
    use HasUuids;

    protected $fillable = [
        'file_name',
        'file_url',
        'file_path',
        'file_type',
        'file_size',
    ];

    // Relasi ke ProfileImage
    public function villageProfiles(): HasMany
    {
        return $this->hasMany(VillageProfile::class);
    }


    // Relasi ke HeadOfFamilyFile
    public function headOfFamilyFile(): HasOne
    {
        return $this->hasOne(HeadOfFamily::class);
    }

    // Relasi ke FamilyMemberFile
    public function familyMemberFile(): HasOne
    {
        return $this->hasOne(FamilyMember::class);
    }
}
