<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\SocialAssistance;
use App\Models\User;

class SocialAssistancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SocialAssistance $socialAssistance): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function update(User $user, SocialAssistance $socialAssistance): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function delete(User $user, SocialAssistance $socialAssistance): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
