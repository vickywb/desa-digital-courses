<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\HeadOfFamily;
use App\Models\User;

class HeadOfFamilyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function view(User $user, HeadOfFamily $headOfFamily): bool
    {
        return $user->role === Role::Admin
            || $user->role === Role::HeadVillage
            || $user->role === Role::Staff
            || $user->headOfFamily?->id === $headOfFamily->id;
    }

    public function update(User $user, HeadOfFamily $headOfFamily): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
