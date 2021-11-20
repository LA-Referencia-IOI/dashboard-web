<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Manager = 1;
    const User = 2;
}
