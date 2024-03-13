<?php

declare(strict_types=1);

namespace App\Enum;

enum StateType: int
{
    case New = 0;
    case Learning = 1;
}
