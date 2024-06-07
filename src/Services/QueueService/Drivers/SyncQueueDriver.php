<?php

namespace Saritasa\LaravelMetrics\Services\QueueService\Drivers;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\SyncQueue;

/**
 * Driver for sync queue.
 */
class SyncQueueDriver implements QueueDriver
{
    /**
     * Driver for sync queue.
     *
     * @param QueueContract $queue Queue object
     *
     * @throws QueueDriverException
     */
    public function __construct(protected QueueContract $queue)
    {
        if (!$this->queue instanceof SyncQueue) {
            throw new QueueDriverException();
        }
    }

    /**
     * Get size of queue.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->queue->size();
    }
}