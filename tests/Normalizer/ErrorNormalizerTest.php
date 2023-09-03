<?php

namespace App\Tests\Normalizer;

use App\Exception\ApiException;
use PHPUnit\Framework\TestCase;
use App\Normalizer\ErrorNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorNormalizerTest extends TestCase
{
    public function testNormalize()
    {
        $normalizer = new ErrorNormalizer();

        $exceptionDetails = 'Exception message';
        $exceptionStatusCode = Response::HTTP_NOT_FOUND;

        $exception = new ApiException($exceptionDetails, $exceptionStatusCode);

        $context = ['debug' => true];

        // Call the normalize method
        $normalizedData = $normalizer->normalize($exception, 'json', $context);

        // Assertions
        $this->assertArrayHasKey('timestamp', $normalizedData);
        $this->assertArrayHasKey('message', $normalizedData);
        $this->assertArrayHasKey('status', $normalizedData);
        $this->assertArrayHasKey('code', $normalizedData);
        $this->assertArrayHasKey('details', $normalizedData);
        $this->assertArrayHasKey('trace', $normalizedData);

        $this->assertEquals($exceptionStatusCode, $normalizedData['status']);
        $this->assertEquals(0, $normalizedData['code']);
        $this->assertEquals(Response::$statusTexts[$exceptionStatusCode], $normalizedData['message']);
        $this->assertEquals($exceptionDetails, $normalizedData['details']);
    }

    public function testSupportsNormalization()
    {
        $normalizer = new ErrorNormalizer();

        // Create a FlattenException instance
        $exception = new ApiException('');
        $flattenException = FlattenException::createFromThrowable($exception);

        // Assert that supportsNormalization returns true for FlattenException
        $this->assertTrue($normalizer->supportsNormalization($flattenException));
    }
}
