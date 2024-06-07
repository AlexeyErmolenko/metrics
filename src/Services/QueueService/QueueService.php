<?php

namespace Saritasa\LaravelMetrics\Services\QueueService;

use Saritasa\LaravelMetrics\Services\QueueService\Drivers\DatabaseQueueDriver;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriver;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriverException;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\SyncQueueDriver;
use Illuminate\Contracts\Queue\Queue as QueueContract;

/**
 * Service to get Queue metrics.
 */
class QueueService
{
    private  const
        DATABASE = 'database',
        SYNC = 'sync';

    /**
     * Service to get Queue metrics.
     *
     * @param QueueContract $queue
     */
    public function __construct(protected QueueContract $queue)
    {
    }

    /**
     * Get queue size.
     *
     * @return int
     *
     * @throws QueueDriverException
     */
    public function getQueueSize(): int
    {
        $queueDriver = $this->prepareDriver();

        return $queueDriver->getSize();
    }

    /**
     * Prepare driver for queue.
     *
     * @return QueueDriver
     *
     * @throws QueueDriverException
     */
    private function prepareDriver(): QueueDriver
    {
        $connectionName = $this->queue->getConnectionName();

        return match ($connectionName) {
            self::DATABASE => new DatabaseQueueDriver($this->queue),
            default => new SyncQueueDriver($this->queue),
        };
    }
}
