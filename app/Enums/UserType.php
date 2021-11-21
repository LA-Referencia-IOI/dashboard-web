<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Manager = 1;
    const User = 2;

    public static function getDescription($value): string
    {
        if ($value === self::Administrator) {
            return 'Administrador';
        }

        if ($value === self::Manager) {
            return 'Gerente';
        }

        if ($value === self::User) {
            return 'Usuário';
        }

        return parent::getDescription($value);
    }
}
