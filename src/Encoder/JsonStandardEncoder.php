<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Enum\JsonStandardStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonStandardEncoder implements EncoderInterface
{
    public function encode(mixed $normalizedData, Request $request, Response $response): mixed
    {
        return [
            '@timestamp' => (new \DateTime())->format(\DateTime::ATOM),
            '@status' => $response->isSuccessful() ? JsonStandardStatus::VALID : JsonStandardStatus::INVALID,
            'data' => $normalizedData,
        ];
    }
}
