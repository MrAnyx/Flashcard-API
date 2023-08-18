<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(
        string $message,
        // ExceptionCode $code,
        ExceptionStatus $statusCode = ExceptionStatus::BAD_REQUEST,
        \Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode->value, $message, $previous, $headers);
    }
}
