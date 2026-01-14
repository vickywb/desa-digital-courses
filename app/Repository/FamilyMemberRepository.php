<?php

namespace App\Repository;

use App\Models\FamilyMember;

class FamilyMemberRepository
{
    private $familyMember;

    public function __construct(FamilyMember $familyMember) {
        $this->familyMember = $familyMember;
    }

    public function save(FamilyMember $familyMember)
    {
        $familyMember->save();

        return $familyMember;
    }
}