<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(
        string $message,
        int $statusCode,
        array $messageParams = [],
        \Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, vsprintf($message, $messageParams), $previous, $headers);
    }
}
