<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case HeadOfFamily = 'head_of_family';
    case FamilyMember = 'family_member';
    case HeadVillage = 'head_village';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::HeadVillage => 'Kepala Desa',
            self::HeadOfFamily => 'Kepala Keluarga',
            self::FamilyMember => 'Anggota Keluarga',
        };
    }
}
