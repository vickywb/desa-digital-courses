<?php

namespace App\Services;

use App\Repository\VillageProfileRepository;

class VillageProfileService
{
    private $villageProfileRepository;

    public function __construct(VillageProfileRepository $villageProfileRepository)
    {
        $this->villageProfileRepository = $villageProfileRepository;
    }
}