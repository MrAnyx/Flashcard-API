<?php

namespace App\Normalizer;

use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{
    public function normalize($exception, string $format = null, array $context = []): array
    {
        $format = [
            'timestamp' => (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::ATOM),
            'message' => Response::$statusTexts[$exception->getStatusCode()],
            'status' => $exception->getStatusCode(),
            'code' => $exception->getCode(),
        ];

        if ($context['debug']) {
            $format['details'] = $exception->getMessage();
            $format['trace'] = $exception->getTrace();
        }

        return $format;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }
}
