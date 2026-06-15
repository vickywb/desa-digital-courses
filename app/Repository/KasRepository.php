<?php

namespace App\Repository;

use App\Models\Kas;

class KasRepository
{
    public function __construct(private Kas $kas) {}

    public function save(Kas $kas): Kas
    {
        $kas->save();

        return $kas->fresh();
    }

    public function findByVillageProfileId(string $villageProfileId): ?Kas
    {
        return $this->kas->where('village_profile_id', $villageProfileId)->first();
    }
}
