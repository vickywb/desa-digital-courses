<?php

namespace App\Enums;

enum FamilyRelation: string
{
    Case Child = 'child';
    Case Wife = 'wife';
    Case HeadFamily = 'head';

    public function label(): string
    {
        return match($this) {
            self::Child => 'Anak',
            self::Wife => 'Istri',
            self::HeadFamily => 'Kepala Keluarga',
        };
    }
}
