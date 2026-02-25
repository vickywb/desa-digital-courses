<?php

namespace App\Services;

use App\Repository\SocialAssistanceRepository;

class SocialAssistanceService
{
    public function __construct(private SocialAssistanceRepository $socialAssistanceRepository) {
    }
}