<?php

namespace Saritasa\LaravelMetrics\Services\QueueService\Drivers;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\RedisQueue;
use Illuminate\Redis\Connections\Connection;

/**
 * Driver for redis queue.
 */
class RedisQueueDriver implements QueueDriver
{
    /**
     * Connection to redis.
     *
     * @var Connection
     */
    private Connection $redis;

    /**
     * Driver for redis queue.
     *
     * @param QueueContract $queue Queue object
     *
     * @throws QueueDriverException
     */
    public function __construct(protected QueueContract $queue)
    {
        if (!$this->queue instanceof RedisQueue) {
            throw new QueueDriverException();
        }

        $this->redis = $this->queue->getConnection();
    }

    /**
     * Get size of queue.
     *
     * @return int
     */
    public function getSize(): int
    {
        $size = 0;
        $queueList = $this->prepareQueueKeyList();

        foreach ($queueList as $queue) {
            $size += $this->queue->size($queue);
        }

        return $size;
    }

    /**
     * Prepare queue key list.
     *
     * @return array
     */
    private function prepareQueueKeyList(): array
    {
        $keyList = [];
        $allKeyList = $this->redis->command('keys', ['queues:*']);

        if (!is_array($allKeyList)) {
            return $keyList;
        }

        foreach ($allKeyList as $key) {
            if (!is_string($key)) {
                continue;
            }

            $list = explode(':', $key);

            if (count($list) != 2) {
                continue;
            }

            $keyList[] = end($list);
        }

        return $keyList;
    }
}
