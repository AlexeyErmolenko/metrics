<?php

namespace Saritasa\LaravelMetrics\Test\Feature;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Saritasa\LaravelMetrics\Test\Support\TestJob;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests to check Queue metrics with sync driver.
 */
class SyncQueueMetricsTest extends FeatureTestCase
{
    /**
     * @return void
     */
    public function testIndex(): void
    {
        TestJob::dispatch(1);
        TestJob::dispatch(2)->onQueue('test1');
        TestJob::dispatch(3)->onQueue('test2');

        $response = $this->call(self::GET, 'metrics');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertArrayHasKey('queueSize', $response->json());
        $this->assertSame(0, $response->json()['queueSize']);
    }

    /**
     * Define environment setup.
     *
     * @param Application $app Application
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $config = $app['config'];

        if (!$config instanceof Repository) {
            return;
        }

        $this->setQueueConfig($config);
    }

    /**
     * Set queue config.
     *
     * @param Repository $config App config
     *
     * @return void
     */
    protected function setQueueConfig(Repository $config): void
    {
        $config->set('queue.default', 'sync');
    }
}
