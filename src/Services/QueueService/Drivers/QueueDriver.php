<?php

namespace Saritasa\LaravelMetrics\Services\QueueService\Drivers;

/**
 * Interface for queue driver.
 */
interface QueueDriver
{
    /**
     * Get queue size.
     *
     * @return int
     */
    public function getSize(): int;
}
