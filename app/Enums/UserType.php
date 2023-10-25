<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Instituition = 1;
    const User = 2;

    public static function getDescription($value): string
    {
        if ($value === self::Administrator) {
            return 'Administrator';
        }

        if ($value === self::Instituition) {
            return 'Instituition';
        }

        if ($value === self::User) {
            return 'User';
        }

        return parent::getDescription($value);
    }
}
