<?php

declare(strict_types=1);

namespace App\Encoder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface EncoderInterface
{
    public function encode(mixed $normalizedData, Request $request, Response $response): mixed;
}
