<?php

declare(strict_types=1);

use App\Logger\ExtraLogProcessor;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ExtraLogProcessorTest extends TestCase
{
    public function testProcessorAddsExtraFieldsToLogRecord(): void
    {
        // Create a RequestStack mock and set it up to return a Request object
        $request = Request::create('/example', 'GET');
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        // Create an instance of ExtraLogProcessor
        $processor = new ExtraLogProcessor($requestStack);

        // Create a Monolog Logger instance and add the processor to it
        $logger = new Logger('test');
        $logger->pushProcessor($processor);

        // Create a TestHandler to capture log records
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        // Log a test message
        $logger->info('Test message');

        // Get the log records
        $logRecords = $handler->getRecords();

        // Ensure that the log record contains the expected extra fields
        $this->assertCount(1, $logRecords);
        $logRecord = $logRecords[0];
        $this->assertArrayHasKey('ip', $logRecord['extra']);
        $this->assertArrayHasKey('method', $logRecord['extra']);
        $this->assertSame('GET', $logRecord['extra']['method']);
        $this->assertArrayHasKey('url', $logRecord['extra']);
        $this->assertSame('/example', $logRecord['extra']['url']);
    }

    public function testProcessorHandlesNullRequest(): void
    {
        // Create a RequestStack mock and set it up to return null
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        // Create an instance of ExtraLogProcessor
        $processor = new ExtraLogProcessor($requestStack);

        // Create a Monolog Logger instance and add the processor to it
        $logger = new Logger('test');
        $logger->pushProcessor($processor);

        // Create a TestHandler to capture log records
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        // Log a test message
        $logger->info('Test message');

        // Get the log records
        $logRecords = $handler->getRecords();

        // Ensure that the log record contains the expected extra fields
        $this->assertCount(1, $logRecords);
        $logRecord = $logRecords[0];
        $this->assertEquals('-', $logRecord['extra']['ip']);
        $this->assertEquals('-', $logRecord['extra']['method']);
        $this->assertEquals('-', $logRecord['extra']['url']);
    }
}
