<?php

declare(strict_types=1);

namespace App\Encoder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonEncoder implements EncoderInterface
{
    public function encode(mixed $data, Request $request, Response $response): mixed
    {
        return $data;
    }
}
