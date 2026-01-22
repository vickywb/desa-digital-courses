<?php

namespace App\Service;

use App\Repository\SocialAssistanceRepository;

class SocialAssistanceService
{
    public function __construct(SocialAssistanceRepository $socialAssistanceRepository) {
        $this->socialAssistanceRepository = $socialAssistanceRepository;
    }
}