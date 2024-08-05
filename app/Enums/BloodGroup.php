<?php

namespace App\Enums;

class BloodGroup
{
    const A_POSITIVE = 'A+';
    const A_NEGATIVE = 'A-';
    const B_POSITIVE = 'B+';
    const B_NEGATIVE = 'B-';
    const AB_POSITIVE = 'AB+';
    const AB_NEGATIVE = 'AB-';
    const O_POSITIVE = 'O+';
    const O_NEGATIVE = 'O-';
    const ALL = 'A+,A-,B+,B-,AB+,AB-,O+,O-';
    
    public static function all(): array
    {
        return [
            self::A_POSITIVE,
            self::A_NEGATIVE,
            self::B_POSITIVE,
            self::B_NEGATIVE,
            self::AB_POSITIVE,
            self::AB_NEGATIVE,
            self::O_POSITIVE,
            self::O_NEGATIVE,
        ];
    }
}
