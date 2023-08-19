<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(
        string $message,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        \Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, $message, $previous, $headers);
    }
}
