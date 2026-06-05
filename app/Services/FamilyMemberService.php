<?php

namespace App\Services;

use App\Repository\FamilyMemberRepository;

class FamilyMemberService
{
    public function __construct(private FamilyMemberRepository $familyMemberRepository) {}
}
