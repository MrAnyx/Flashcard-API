<?php

declare(strict_types=1);

namespace App\Exception\Http;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException as SymfonyUnauthorizedHttpException;

class UnauthorizedHttpException extends SymfonyUnauthorizedHttpException
{
    public function __construct(string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct('bearer', $message, $previous, $headers, $code);
    }
}
