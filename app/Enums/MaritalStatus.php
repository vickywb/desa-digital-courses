<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Widow = 'widow';
    case Widower = 'widower';
    
    public function label(): string
    {
        return match($this) {
            self::Single => 'Belum Menikah',
            self::Married => 'Menikah',
            self::Widow => 'Janda',
            self::Widower => 'Duda',
        };
    }
}