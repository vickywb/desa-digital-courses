<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'thumbnail',
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
    public function profileFiles()
    {
        return $this->hasMany(ProfileFile::class);
    }
}
