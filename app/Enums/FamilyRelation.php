<?php

namespace App\Enums;

enum FamilyRelation: string
{
    Case Child = 'child';
    Case Wife = 'wife';
    Case Husband = 'husband';

    public function label(): string
    {
        return match($this) {
            self::Child => 'Anak',
            self::Wife => 'Istri',
            self::Husband => 'Suami',
        };
    }
}
