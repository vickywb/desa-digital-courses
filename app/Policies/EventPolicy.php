<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function update(User $user, Event $event): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }

    public function delete(User $user, Event $event): bool
    {
        return in_array($user->role, [Role::Admin, Role::HeadVillage, Role::Staff]);
    }
}
