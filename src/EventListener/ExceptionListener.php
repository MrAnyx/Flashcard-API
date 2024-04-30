<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\JsonStandardStatus;
use App\Model\JsonStandard;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener]
class ExceptionListener
{
    public function __construct(
        public NormalizerInterface $normalizer
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        $format = [
            // 'code' => $exception->getCode(),
            'message' => Response::$statusTexts[$statusCode],
            'details' => $exception->getMessage(),
        ];

        $response = new JsonResponse(
            $this->normalizer->normalize(
                new JsonStandard($format, JsonStandardStatus::INVALID),
                'json',
                ['groups' => [JsonStandard::DEFAULT_GROUP]]
            ),
            $statusCode);

        $event->setResponse($response);
    }
}
