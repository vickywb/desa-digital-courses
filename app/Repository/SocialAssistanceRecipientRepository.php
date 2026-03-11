<?php

namespace App\Repository;

use App\Models\SocialAssistanceRecipient;

class SocialAssistanceRecipientRepository
{
    public function __construct(private SocialAssistanceRecipient $socialAssistanceRecipient) {}
    
    public function save(SocialAssistanceRecipient $socialAssistanceRecipient)
    {
        $socialAssistanceRecipient->save();

        return $socialAssistanceRecipient;
    }
}