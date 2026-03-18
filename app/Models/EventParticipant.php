<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventParticipant extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'event_id',
        'head_of_family_id',
        'family_member_id',
        'quantity',
        'total_price',
        'payment_status',
    ];

    protected $casts = [
        'total_price' => 'string',
    ];

    // Relasi dengan model Event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi dengan model HeadOfFamily
    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }

    // Relasi dengan model FamilyMember
    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }
}
