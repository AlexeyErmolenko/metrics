<?php

namespace Saritasa\LaravelMetrics\Services\QueueService\Drivers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\DatabaseQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

/**
 * Driver for database queue.
 */
class DatabaseQueueDriver implements QueueDriver
{
    /**
     * Driver for database queue.
     *
     * @param QueueContract $queue Queue object
     *
     * @throws QueueDriverException
     */
    public function __construct(protected QueueContract $queue)
    {
        if (!$this->queue instanceof DatabaseQueue) {
            throw new QueueDriverException();
        }
    }

    /**
     * Get size of queue.
     *
     * @return int
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getSize(): int
    {
        $queueList = $this->getQueueList();

        if ($queueList->isEmpty()) {
            return $this->queue->size();
        }

        $size = 0;

        $queueList->each(function (stdClass $item) use (&$size) {
            if (isset($item->queue)) {
                $size += $this->queue->size($item->queue);
            }
        });

        return $size;
    }

    /**
     * Get names of queue.
     *
     * @return Collection
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getQueueList(): Collection
    {
        $config = app(Repository::class);
        $table = $config->get('queue.connections.database.table');

        return DB::query()
            ->select(['queue'])
            ->from($table)
            ->groupBy('queue')
            ->get();
    }
}
