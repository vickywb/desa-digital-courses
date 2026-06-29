<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class File extends Model
{
    use HasUuids;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    // Relasi ke VillageProfile melalui tabel pivot village_profile_files
    public function villageProfiles(): BelongsToMany
    {
        return $this->belongsToMany(VillageProfile::class, 'village_profile_files', 'file_id', 'village_profile_id');
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

    // Relasi ke SocialAssistanceRecipientFile
    public function socialAssistanceRecipientFile(): HasOne
    {
        return $this->hasOne(SocialAssistanceRecipient::class);
    }
}
