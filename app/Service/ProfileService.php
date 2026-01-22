<?php

namespace App\Service;

use App\Repository\ProfileRepository;

class ProfileService
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }
}