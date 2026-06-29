<?php

namespace App\Enums;

enum FamilyRelation: string
{
    case Child = 'child';
    case Wife = 'wife';
    case HeadFamily = 'head';

    public function label(): string
    {
        return match ($this) {
            self::Child => 'Anak',
            self::Wife => 'Istri',
            self::HeadFamily => 'Kepala Keluarga',
        };
    }
}
