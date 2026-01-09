<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeadOfFamilyFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'head_of_family_id',
        'file_id',
    ];

    // Relasi ke HeadOfFamily
    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
