<?php

namespace App\Tests\Service;

use App\Exception\JsonException;
use App\Service\RequestPayloadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RequestPayloadServiceTest extends KernelTestCase
{
    public function testGetRequestPayloadValidJson()
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

    public function testGetRequestPayloadInvalidJson()
    {
        /** @var RequestPayloadService $service */
        $service = self::getContainer()->get(RequestPayloadService::class);
        $request = Request::create('/', 'POST', [], [], [], [], 'invalid_json');
        $this->expectException(JsonException::class);
        $service->getRequestPayload($request);
    }

    public function testGetQueryPayload()
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
