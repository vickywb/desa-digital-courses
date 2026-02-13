<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadOfFamily extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $fillable = [
        'user_id',
        'file_id',
        'full_name',
        'identity_number',
        'gender',
        'date_of_birth',
        'phone_number',
        'occupation',
        'marital_status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => Gender::class,
        'marital_status' => MaritalStatus::class
    ];

    // Relasi dengan model File
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    // Relasi dengan model User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model FamilyMember
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    // Relasi ke SocialAssistanceRecipient
    public function socialAssistanceRecipients(): HasMany
    {
        return $this->hasMany(SocialAssistanceRecipient::class);
    }

    // Relasi ke EventParticipant
    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }
    
    // Accesor untuk file URL
    public function getProfileUrlAttribute()
    {
        return $this->file
            ? $this->file->file_url
            : asset('images/no-image.png');
    }
}
