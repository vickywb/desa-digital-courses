<?php

namespace App\Repository;

use App\Models\SocialAssistanceRecipient;

class SocialAssistanceRecipientRepository
{
    private $socialAssistanceRecipient;

    public function __construct(SocialAssistanceRecipient $socialAssistanceRecipient) {
        $this->socialAssistanceRecipient = $socialAssistanceRecipient;
    }
    
    public function save(SocialAssistanceRecipient $socialAssistanceRecipient)
    {
        $socialAssistanceRecipient->save();

        return $socialAssistanceRecipient;
    }
}