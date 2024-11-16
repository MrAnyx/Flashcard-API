<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Encoder\EncoderInterface;
use App\Encoder\JsonEncoder;
use App\Encoder\JsonStandardEncoder;
use App\Model\ResponseFormat;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    private const FORMATS = [
        'jsonstd' => ['application/json+std'],
        'json' => 'application/json',
    ];

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $acceptHeaders = $request->getAcceptableContentTypes();
        $format = $this->getResponseFormat($acceptHeaders) ?? new ResponseFormat('jsonstd', 'application/json+std');

        /** @var EncoderInterface $encoder */
        $encoder = match ($format->format) {
            'json' => new JsonEncoder(),
            'jsonstd' => new JsonStandardEncoder(),
            default => throw new \RuntimeException(\sprintf('Can not find the corresponding response encoder for format %s', $format->format)),
        };

        $data = $encoder->encode(json_decode($response->getContent(), true), $request, $response);

        $response = new JsonResponse(
            $data,
            $response->getStatusCode(),
            array_merge($response->headers->all(), ['Content-Type' => $format->mimeType]),
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

    private function getResponseFormat(array $acceptHeaders): ?ResponseFormat
    {
        foreach ($acceptHeaders as $acceptMimeType) {
            foreach (self::FORMATS as $format => $mimeType) {
                if (is_iterable($mimeType)) {
                    if (\in_array($acceptMimeType, $mimeType)) {
                        return new ResponseFormat($format, $acceptMimeType);
                    }
                } else {
                    if ($acceptMimeType === $mimeType) {
                        return new ResponseFormat($format, $acceptMimeType);
                    }
                }
            }
        }

        return null;
    }
}
