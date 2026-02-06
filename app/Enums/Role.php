<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case HeadOfFamily = 'head_of_family';
    case FamilyMember = 'family_member';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::HeadOfFamily => 'Kepala Keluarga',
            self::FamilyMember => 'Anggota Keluarga',
        };
    }
}
