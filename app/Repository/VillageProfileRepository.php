<?php

namespace App\Repository;

use App\Models\VillageProfile;

class VillageProfileRepository
{
    private $villageProfile;

    public function __construct(VillageProfile $villageProfile) {
        $this->villageProfile = $villageProfile;
    }

    public function save(VillageProfile $villageProfile)
    {
        $villageProfile->save();

        return $villageProfile;
    }
}