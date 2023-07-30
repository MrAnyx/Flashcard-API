<?php

namespace App\Logger;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\RequestStack;

class ExtraLogProcessor
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(LogRecord $record)
    {
        $request = $this->requestStack->getCurrentRequest();
        $serverParams = $request?->server ?? null;

        $record['extra']['ip'] = $serverParams?->has('REMOTE_ADDR') ? IpUtils::anonymize($serverParams?->get('REMOTE_ADDR')) : '-';
        $record['extra']['method'] = $serverParams?->has('REQUEST_METHOD') ? $serverParams?->get('REQUEST_METHOD') : '-';
        $record['extra']['url'] = $serverParams?->has('REQUEST_URI') ? $serverParams?->get('REQUEST_URI') : '-';

        return $record;
    }
}
