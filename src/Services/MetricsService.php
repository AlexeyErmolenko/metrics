<?php

namespace Saritasa\LaravelMetrics\Services;

use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriverException;
use Saritasa\LaravelMetrics\Services\QueueService\QueueService;

/**
 * Service to get metrics of application.
 */
class MetricsService
{
    /**
     * Service to get metrics of application.
     *
     * @param QueueService $queueService Queue service
     */
    public function __construct(protected QueueService $queueService)
    {
    }

    /**
     * Get application metrics.
     *
     * @return array<string, mixed>
     *
     * @throws QueueDriverException
     */
    public function getMetrics(): array
    {
        return [
            'queueSize' => $this->queueService->getQueueSize(),
        ];
    }
}
