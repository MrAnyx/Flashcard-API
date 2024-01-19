<?php

namespace App\EventListener;

use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        $format = [
            'timestamp' => (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::ATOM),
            'status' => $statusCode,
            // 'code' => $exception->getCode(),
            'message' => Response::$statusTexts[$statusCode],
            'details' => $exception->getMessage(),
            'uri' => $request->getRequestUri(),
        ];

        $response = new JsonResponse($format, $statusCode);

        $event->setResponse($response);
    }
}
