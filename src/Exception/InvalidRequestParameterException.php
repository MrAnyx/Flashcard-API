<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRequestParameterException extends HttpException
{
    public const CODE = 400_0;

    public const STATUS = 400;

    public function __construct(string $message = '', \Throwable $previous = null, array $headers = [])
    {
        parent::__construct(self::STATUS, $message, $previous, $headers, self::CODE);
    }
}
