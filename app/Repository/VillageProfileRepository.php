<?php

namespace App\Repository;

use App\Models\VillageProfile;

class VillageProfileRepository
{
    public function __construct(private VillageProfile $villageProfile) {}

    public function save(VillageProfile $villageProfile): VillageProfile
    {
        $villageProfile->save();

        return $villageProfile->fresh();
    }
}