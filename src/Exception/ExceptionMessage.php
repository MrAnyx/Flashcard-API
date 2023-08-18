<?php

namespace App\Exception;

class ExceptionMessage
{
    // DUP - Duplication
    public const EMAIL_DUPLICATION = 'DUP_1';

    public const USERNAME_DUPLICATION = 'DUP_2';

    // INV - Invalid
    public const JWT_REFRESH_TOKEN_NOT_FOUND = 'INV_1';

    public const JWT_TOKEN_NOT_FOUND = 'INV_2';

    public const JWT_TOKEN_EXPIRED = 'INV_3';

    // ERR - Error
    public const JWT_REFRESH_TOKEN_FAILURE = 'ERR_1';
}
