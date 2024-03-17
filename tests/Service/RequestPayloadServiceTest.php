<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\JsonException;
use App\Service\RequestPayloadService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestPayloadServiceTest extends KernelTestCase
{
    public function testGetRequestPayloadValidJson(): void
    {
        /** @var RequestPayloadService $service */
        $service = self::getContainer()->get(RequestPayloadService::class);

        $payload = [
            'key1' => 'value1',
            'key2' => [
                'key21' => 'value21',
            ],
        ];

        $request = Request::create('/', 'POST', [], [], [], [], json_encode($payload));
        $result = $service->getRequestPayload($request);
        $this->assertSame($payload, $result);
    }

    public function testGetRequestPayloadInvalidJson(): void
    {
        /** @var RequestPayloadService $service */
        $service = self::getContainer()->get(RequestPayloadService::class);
        $request = Request::create('/', 'POST', [], [], [], [], 'invalid_json');
        $this->expectException(JsonException::class);
        $service->getRequestPayload($request);
    }

    public function testGetQueryPayload(): void
    {
        $service = new RequestPayloadService();
        $request = Request::create('/?param1=value1&param2=value2');
        $result = $service->getQueryPayload($request);
        $this->assertSame(['param1' => 'value1', 'param2' => 'value2'], $result);

        $request = Request::create('/?param[]=1&param[]=value2');
        $result = $service->getQueryPayload($request);
        $this->assertSame(['param' => ['1', 'value2']], $result);
    }
}
