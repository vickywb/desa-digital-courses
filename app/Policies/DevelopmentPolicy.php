<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Development;
use App\Models\User;

class DevelopmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Development $development): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function update(User $user, Development $development): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function delete(User $user, Development $development): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
