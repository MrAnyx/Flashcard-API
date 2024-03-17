<?php

declare(strict_types=1);

namespace App\Utility;

class Regex
{
    public const INTEGER = '\\d+';

    public const USERNAME = '^[\\w\\-\\.]*$';

    public const USERNAME_SLASH = '/' . self::USERNAME . '/';
}
