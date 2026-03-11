<?php

namespace App\Repository;

use App\Models\FamilyMember;

class FamilyMemberRepository
{
    public function __construct(private FamilyMember $familyMember) {}

    public function save(FamilyMember $familyMember)
    {
        $familyMember->save();

        return $familyMember;
    }
}