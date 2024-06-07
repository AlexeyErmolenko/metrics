<?php

namespace Saritasa\LaravelMetrics\Test\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Job to test.
 */
class TestJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Job to test.
     *
     * @param int $id Test identifier
     */
    public function __construct(protected int $id)
    {
    }

    /**
     * Execute job.
     *
     * @return void
     */
    public function handle(): void
    {
    }
}