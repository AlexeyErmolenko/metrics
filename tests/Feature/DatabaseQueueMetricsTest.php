<?php

namespace Saritasa\LaravelMetrics\Test\Feature;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Saritasa\LaravelMetrics\Test\Support\TestJob;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests to check Queue metrics with database driver.
 */
class DatabaseQueueMetricsTest extends FeatureTestCase
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
        $this->assertSame(3, $response->json()['queueSize']);
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    /**
     * Set up database of environment.
     *
     * @return void
     */
    protected function setUpDatabase(): void
    {
        $this->artisan('queue:table')->run();
        $this->artisan('migrate')->run();
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

        $this->setDatabaseConfig($config, 'sqlite');
        $this->setQueueConfig($config);
    }

    /**
     * Set database config.
     *
     * @param Repository $config App config
     * @param string $type Type pf database
     *
     * @return void
     */
    protected function setDatabaseConfig(Repository $config, string $type): void
    {
        $connectionSettings = match ($type) {
            'sqlite' => ['driver' => $type, 'database' => ':memory:', 'prefix' => ''],
            default => [],
        };

        if (empty($connectionSettings)) {
            return;
        }

        $config->set('database.default', $type);
        $config->set('database.connections.' . $type, $connectionSettings);
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
        $config->set('queue.default', 'database');
        $config->set('queue.connections.database.table', 'jobs');
    }
}
