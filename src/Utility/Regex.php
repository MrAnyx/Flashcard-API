<?php

declare(strict_types=1);

namespace App\Utility;

class Regex
{
    public const INTEGER = '\\d+';

    public const USERNAME = '^[\\w\\-\\.]*$';

    public const USERNAME_SLASH = '/' . self::USERNAME . '/';

    /*
    link: https://stackoverflow.com/a/21052485
    - At least one upper case English letter
    - At least one lower case English letter
    - At least one digit
    - At least one special character
    - Minimum eight in length
    */
    public const PASSWORD = '^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\\d]){1,})(?=(.*[\\W]){1,})(?!.*\\s).{8,}$';

    public const PASSWORD_SLASH = '/' . self::PASSWORD . '/';
}
