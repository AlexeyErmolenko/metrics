<?php

namespace Saritasa\LaravelMetrics\Test\Unit\Services;

use Mockery;
use Saritasa\LaravelMetrics\Services\MetricsService;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriverException;
use Saritasa\LaravelMetrics\Services\QueueService\QueueService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Saritasa\LaravelMetrics\Services\MetricsService
 */
class MetricsServiceTest extends TestCase
{
    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetMetrics(): void
    {
        $size = rand(1, 1000);

        $queueServiceMock = Mockery::mock(QueueService::class)
            ->shouldReceive('getQueueSize')
            ->once()
            ->andReturns($size)
            ->getMock();

        $instance = new MetricsService($queueServiceMock);

        $this->assertSame(['queueSize' => $size], $instance->getMetrics());
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetMetricsWithException(): void
    {
        $queueServiceMock = Mockery::mock(QueueService::class)
            ->shouldReceive('getQueueSize')
            ->once()
            ->andThrows(new QueueDriverException())
            ->getMock();

        $instance = new MetricsService($queueServiceMock);

        $this->expectException(QueueDriverException::class);
        $this->expectExceptionMessage('Incorrect connection for driver.');

        $instance->getMetrics();
    }
}
