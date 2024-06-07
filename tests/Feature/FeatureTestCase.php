<?php
namespace Saritasa\LaravelMetrics\Test\Feature;

use Saritasa\LaravelMetrics\Providers\MetricsServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

/**
 * Abstract feature test case.
 */
abstract class FeatureTestCase extends TestCase
{
    protected const GET = 'get';

    /**
     * Get package providers.
     *
     * @param Application $app Application
     *
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [MetricsServiceProvider::class]);
    }


}
