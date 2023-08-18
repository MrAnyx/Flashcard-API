<?php

namespace App\Normalizer;

use DateTime;
use DateTimeZone;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{
    public function normalize($exception, string $format = null, array $context = []): array
    {
        return [
            'timestamp' => (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::ATOM),
            'message' => $exception->getMessage(),
            'status' => $exception->getStatusCode(),
            'code' => $exception->getCode(),
            'trace' => $context['debug'] ? $exception->getTrace() : [],
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }
}
