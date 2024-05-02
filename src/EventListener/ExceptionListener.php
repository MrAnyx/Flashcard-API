<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\JsonStandardStatus;
use App\Model\ErrorStandard;
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

        // 'code' => $exception->getCode(),

        $error = new ErrorStandard(
            Response::$statusTexts[$statusCode],
            $exception->getMessage()
        );

        $response = new JsonResponse(
            $this->normalizer->normalize(
                new JsonStandard($error, JsonStandardStatus::INVALID),
                'json',
            ),
            $statusCode);

        $event->setResponse($response);
    }
}
