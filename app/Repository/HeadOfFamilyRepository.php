<?php

namespace App\Repository;

use App\Models\HeadOfFamily;

class HeadOfFamilyRepository
{
    public function __construct(private HeadOfFamily $headOfFamily) {}

    public function save(HeadOfFamily $headOfFamily)
    {
        $headOfFamily->save();

        return $headOfFamily;
    }
}