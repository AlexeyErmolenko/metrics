<?php

namespace Saritasa\LaravelMetrics\Test\Feature;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Saritasa\LaravelMetrics\Test\Support\TestJob;
use Symfony\Component\HttpFoundation\Response;

class RedisQueueMetricsTest extends FeatureTestCase
{
    /**
     * @return void
     */
    public function testIndex(): void
    {
        $keysList =  [
            'laravel_queues:default',
            'laravel_queues:default:notify',
            'laravel_queues:test1',
            'laravel_queues:test1:notify',
            'laravel_queues:test2',
            'laravel_queues:test2:notify',
        ];
        $keyValue = [
            'default' => 1,
            'test1' => 1,
            'test2' =>2,
        ];

        Redis::shouldReceive('connection')
            ->andReturns(Mockery::self())
            ->getMock()
            ->shouldReceive('eval')
            ->times(4)
            ->getMock()
            ->shouldReceive('command')
            ->withArgs(function (string $arg1, array $arg2): bool {
                $this->assertSame('keys', $arg1);
                $this->assertSame(['queues:*'], $arg2);

                return true;
            })
            ->andReturns($keysList)
            ->getMock()
            ->shouldReceive('eval')
            ->times(count($keyValue))
            ->andReturns($keyValue['default'], $keyValue['test1'], $keyValue['test2'])
            ->getMock();
        TestJob::dispatch(1);
        TestJob::dispatch(2)->onQueue('test1');
        TestJob::dispatch(3)->onQueue('test2');
        TestJob::dispatch(4)->onQueue('test2');


        $response = $this->call(self::GET, 'metrics');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertArrayHasKey('queueSize', $response->json());
        $this->assertSame(4, $response->json()['queueSize']);

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
        $config->set('queue.default', 'redis');
    }
}
