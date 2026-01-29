<?php

namespace App\Models;

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
}
