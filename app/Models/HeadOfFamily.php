<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadOfFamily extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'profile_picture',
        'identify_number',
        'gender',
        'date_of_birth',
        'phone_number',
        'occupation',
        'marital_status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

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
}
