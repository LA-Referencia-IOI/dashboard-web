<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class InstitutionType extends Enum
{
    const Government = 0;
    const University = 1;
    const Library = 2;
    const Others = 3;

    public static function getDescription($value): string
    {
        if ($value == self::Government) {
            return 'Government';
        }
        else if ($value == self::University) {
            return 'University';
        }
        else if ($value == self::Library) {
            return 'Library';
        }else if ($value == self::Others){
            return 'Others';
        }else{
            return 'Undefined';
        }

        return parent::getDescription($value);
    }
}