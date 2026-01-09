<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasUuid;

    protected $fillable = [
        'file_name',
        'file_url',
        'file_path',
        'file_type',
        'file_size',
    ];

    // Relasi ke ProfileImage
    public function profileImages(): HasMany
    {
        return $this->hasMany(ProfileImage::class);
    }

    // Relasi ke DevelopmentFile
    public function developmentFiles(): HasMany
    {
        return $this->hasMany(DevelopmentFile::class);
    }

    // Relasi ke EventFile
    public function eventFiles(): HasMany
    {
        return $this->hasMany(EventFile::class);
    }

    // Relasi ke SocialAssistanceFile
    public function socialAssistanceFiles(): HasMany
    {
        return $this->hasMany(SocialAssistanceFile::class);
    }

    // Relasi ke HeadOfFamilyFile
    public function headOfFamilyFiles(): HasMany
    {
        return $this->hasMany(HeadOfFamilyFile::class);
    }

    // Relasi ke FamilyMemberFile
    public function familyMemberFiles(): HasMany
    {
        return $this->hasMany(FamilyMemberFile::class);
    }
}
