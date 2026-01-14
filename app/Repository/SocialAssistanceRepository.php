<?php

namespace App\Repository;

use App\Models\SocialAssistance;

class SocialAssistanceRepository
{
    private $socialAssistance;

    public function __construct(SocialAssistance $socialAssistance) {
        $this->socialAssistance = $socialAssistance;
    }

    public function save(SocialAssistance $socialAssistance)
    {
        $socialAssistance->save();

        return $socialAssistance;
    }
}