<?php

namespace App\Enum;

enum StateType: int
{
    case New = 0;
    case Learning = 1;
    case Review = 2;
    case Relearning = 3;
}
