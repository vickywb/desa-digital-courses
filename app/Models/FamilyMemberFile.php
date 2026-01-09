<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMemberFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'family_member_id',
        'file_id',
    ];

    // Relasi ke FamilyMember
    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }

    // Relasi ke File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
