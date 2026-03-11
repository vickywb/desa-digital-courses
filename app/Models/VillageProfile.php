<?php

namespace App\Models;

use App\Models\VillageProfileFile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VillageProfile extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'about',
        'headman',
        'people',
        'agriculture_area',
        'total_area',
    ];

    protected $casts = [
        'agriculture_area' => 'string',
        'total_area' => 'string',
    ];

    // Relasi ke ProfileFile
    public function villageProfileFiles()
    {
        return $this->hasMany(VillageProfile::class);
    }

    // Relasi hasManyThrough untuk mendapatkan file melalui VillageProfileFile
    public function files()
    {
        return $this->hasManyThrough(
            File::class, // Model yang ingin diakses (File)
            VillageProfileFile::class, // Model perantara atau pivot (Village Profile File)
            'village_profile_id', // Foreign Key di VillageProfileFile yang menghubungkan ke Village Profile
            'id', // Primary Key di File
            'id', // Primary key di VillageProfile
            'file_id' // Foreign Key di VillageProfileFile yang menghubungkan ke File
        );
    }
}
