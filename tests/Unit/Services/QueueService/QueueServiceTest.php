<?php

namespace Saritasa\LaravelMetrics\Test\Unit\Services\QueueService;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\DatabaseQueue;
use Illuminate\Queue\RedisQueue;
use Illuminate\Queue\SyncQueue;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\TestCase;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriverException;
use Saritasa\LaravelMetrics\Services\QueueService\QueueService;
use stdClass;

/**
 * @covers \Saritasa\LaravelMetrics\Services\QueueService\QueueService
 */
class QueueServiceTest extends TestCase
{
    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithSync(): void
    {
        $syncQueueMock = Mockery::mock(SyncQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('sync')
            ->getMock()
            ->shouldReceive('size')
            ->once()
            ->andReturns($size = 0)
            ->getMock();

        $instance = $this->prepareInstance($syncQueueMock);

        $this->assertSame($size, $instance->getQueueSize());
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithSyncWithError(): void
    {
        $syncQueueMock = Mockery::mock(DatabaseQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('sync')
            ->getMock();

        $instance = $this->prepareInstance($syncQueueMock);

        $this->expectException(QueueDriverException::class);
        $this->expectExceptionMessage('Incorrect connection for driver.');

        $instance->getQueueSize();
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithDatabase(): void
    {
        $queue = new stdClass();
        $queue->queue = 'default';

        $queueList = collect();
        $queueList->push($queue);

        $dbQueueMock = Mockery::mock(DatabaseQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('database')
            ->getMock()
            ->shouldReceive('size')
            ->once()
            ->andReturns($size = $queueList->count())
            ->getMock();

        DB::shouldReceive('query')
            ->once()
            ->andReturns(Mockery::self())
            ->getMock()
            ->shouldReceive('select')
            ->once()
            ->andReturns(Mockery::self())
            ->getMock()
            ->shouldReceive('from')
            ->once()
            ->andReturns(Mockery::self())
            ->getMock()
            ->shouldReceive('groupBy')
            ->once()
            ->andReturns(Mockery::self())
            ->getMock()
            ->shouldReceive('get')
            ->once()
            ->andReturns($queueList)
        ;

        $instance = $this->prepareInstance($dbQueueMock);

        $this->assertSame($size, $instance->getQueueSize());
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithDatabaseWithError(): void
    {
        $dbQueueMock = Mockery::mock(SyncQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('database')
            ->getMock();

        $instance = $this->prepareInstance($dbQueueMock);

        $this->expectException(QueueDriverException::class);
        $this->expectExceptionMessage('Incorrect connection for driver.');

        $instance->getQueueSize();
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithRedis1(): void
    {
        $keyValue = [
            'default' => rand(1, 100),
            'test1' => rand(1, 100),
            'test2' =>rand(1, 100),
        ];

        $keysList =  [
            'laravel_queues:default',
            'laravel_queues:default:notify',
            'laravel_queues:test1',
            'laravel_queues:test1:notify',
            'laravel_queues:test2',
            'laravel_queues:test2:notify',
        ];

        $redisConnectionMock = Mockery::mock(Connection::class)
            ->shouldReceive('command')
            ->once()
            ->withArgs(function (string $arg1, array $arg2): bool {
                $this->assertSame('keys', $arg1);
                $this->assertSame(['queues:*'], $arg2);

                return true;
            })
            ->andReturns($keysList)
            ->getMock();

        $redisQueueMock = Mockery::mock(RedisQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('redis')
            ->getMock()
            ->shouldReceive('getConnection')
            ->once()
            ->andReturns($redisConnectionMock)
            ->getMock()
            ->shouldReceive('size')
            ->withArgs(function (string $arg) use ($keyValue): bool {
                $this->assertArrayHasKey($arg, $keyValue);

                return true;
            })
            ->andReturns($keyValue['default'], $keyValue['test1'], $keyValue['test2'])
            ->getMock();

        $instance = $this->prepareInstance($redisQueueMock);

        $this->assertSame(array_sum($keyValue), $instance->getQueueSize());
    }

    /**
     * @return void
     *
     * @throws QueueDriverException
     */
    public function testGetQueueSizeWithRedisWithError(): void
    {
        $redisQueueMock = Mockery::mock(SyncQueue::class)
            ->shouldReceive('getConnectionName')
            ->once()
            ->andReturns('redis')
            ->getMock();

        $instance = $this->prepareInstance($redisQueueMock);

        $this->expectException(QueueDriverException::class);
        $this->expectExceptionMessage('Incorrect connection for driver.');

        $instance->getQueueSize();
    }

    /**
     * @param QueueContract $queue
     *
     * @return QueueService
     */
    private function prepareInstance(QueueContract $queue): QueueService
    {
        return new QueueService($queue);
    }
}
