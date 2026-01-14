<?php

namespace App\Repository;

use App\Models\HeadOfFamily;

class HeadOfFamilyRepository
{
    private $headOfFamily;

    public function __construct(HeadOfFamily $headOfFamily)
    {
        $this->headOfFamily = $headOfFamily;
    }

    public function save(HeadOfFamily $headOfFamily)
    {
        $headOfFamily->save();

        return $headOfFamily;
    }
}