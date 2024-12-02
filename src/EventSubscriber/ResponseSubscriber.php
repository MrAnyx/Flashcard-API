<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Encoder\EncoderInterface;
use App\Encoder\JsonEncoder;
use App\Encoder\JsonStandardEncoder;
use App\Enum\ContentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $initialRequest = $event->getRequest();
        $initialResponse = $event->getResponse();

        // Enabled only for project routes, not symfony routes like the profiler
        [$controller, $method] = explode('::', $initialRequest->attributes->get('_controller'));
        if (!class_exists($controller) || !method_exists($controller, $method)) {
            return;
        }

        $acceptHeaders = $initialRequest->getAcceptableContentTypes();
        $format = $this->getResponseFormat($acceptHeaders) ?? ContentType::JSON_STD;
        $encoder = $this->getEncoder($format);

        $data = $encoder->encode(json_decode($initialResponse->getContent(), true), $initialRequest, $initialResponse);

        $response = new JsonResponse(
            $data,
            $initialResponse->getStatusCode(),
            array_merge($initialResponse->headers->all(), ['Content-Type' => $format->value]),
        );

        $event->setResponse($response);
        $event->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    private function getResponseFormat(array $acceptHeaders): ?ContentType
    {
        foreach ($acceptHeaders as $acceptMimeType) {
            if (ContentType::hasValue($acceptMimeType)) {
                return ContentType::tryFrom($acceptMimeType);
            }
        }

        return null;
    }

    private function getEncoder(ContentType $format): EncoderInterface
    {
        return match ($format) {
            ContentType::JSON => new JsonEncoder(),
            ContentType::JSON_STD => new JsonStandardEncoder(),
        };
    }
}
