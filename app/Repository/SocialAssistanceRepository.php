<?php

namespace App\Repository;

use App\Models\SocialAssistance;

class SocialAssistanceRepository
{
    public function __construct(private SocialAssistance $socialAssistance) {}

    public function save(SocialAssistance $socialAssistance)
    {
        $socialAssistance->save();

        return $socialAssistance->fresh();
    }
}