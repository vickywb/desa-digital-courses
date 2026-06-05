<?php

namespace App\Services;

use App\Repository\HeadOfFamilyRepository;

class HeadOfFamilyService
{
    private $headOfFamilyRepository;

    public function __construct(HeadOfFamilyRepository $headOfFamilyRepository)
    {
        $this->headOfFamilyRepository = $headOfFamilyRepository;
    }
}
