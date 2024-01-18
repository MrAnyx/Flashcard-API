<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(
        int $statusCode,
        string $message,
        array $messageParams = [],
        ExceptionCode $exceptionCode = ExceptionCode::GENERIC_ERROR,
        \Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, vsprintf($message, $messageParams), $previous, $headers, $exceptionCode->value);
    }
}
