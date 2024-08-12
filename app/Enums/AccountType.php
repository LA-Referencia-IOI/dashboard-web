<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AccountType extends Enum
{
    const Manager = 0;
    const Partner = 1;
    const Others = 2;

    public static function getDescription($value): string
    {
        if ($value == self::Manager) {
            return 'Manager';
        }
        else if ($value == self::Partner) {
            return 'Partner';
        }else{
            return 'Undefined';
        }

        return parent::getDescription($value);
    }
}