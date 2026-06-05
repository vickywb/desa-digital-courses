<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\VillageProfile;

class VillageProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VillageProfile $villageProfile): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function update(User $user, VillageProfile $villageProfile): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function delete(User $user, VillageProfile $villageProfile): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
