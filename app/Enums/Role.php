<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case HeadOfFamily = 'head_of_family';
    case HeadVillage = 'head_village';
    case Staff = 'staff';

    public function maxQuota(): ?int
    {
        return match ($this) {
            Role::Admin => 1,
            Role::HeadVillage => 1,
            Role::Staff => 3,
            Role::HeadOfFamily => null,
        };
    }
}
