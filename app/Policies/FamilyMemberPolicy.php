<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\FamilyMember;
use App\Models\User;

class FamilyMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff, Role::HeadOfFamily]);
    }

    public function view(User $user, FamilyMember $familyMember): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff])
            || $user->headOfFamily?->id === $familyMember->head_of_family_id;
    }

    public function update(User $user, FamilyMember $familyMember): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
