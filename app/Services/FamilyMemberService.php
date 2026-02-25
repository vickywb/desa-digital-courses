<?php

namespace App\Services;

use App\Repository\FamilyMemberRepository;

class FamilyMemberServices
{
    public function __construct(private FamilyMemberRepository $familyMemberRepository)
    {
    }
}