<?php

namespace App\EventSubscriber;

use App\Exception\ApiException;
use App\Exception\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HttpExceptionEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            throw new ApiException(Response::HTTP_FORBIDDEN, $exception->getMessage(), [], ExceptionCode::UNAUTHORIZED);
        }
    }
}
