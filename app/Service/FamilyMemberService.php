<?php

namespace App\Service;

use App\Repository\FamilyMemberRepository;

class FamilyMemberService
{
    private $familyMemberRepository;

    public function __construct(FamilyMemberRepository $familyMemberRepository)
    {
        $this->familyMemberRepository = $familyMemberRepository;
    }
}