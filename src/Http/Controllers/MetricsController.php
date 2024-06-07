<?php

namespace Saritasa\LaravelMetrics\Http\Controllers;

use Saritasa\LaravelMetrics\Services\MetricsService;
use Saritasa\LaravelMetrics\Services\QueueService\Drivers\QueueDriverException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Metrics controller allows you to get various metrics about the application.
 */
class MetricsController extends Controller
{
    /**
     * @param MetricsService $service Service to get different metrics
     *
     * @return JsonResponse
     *
     * @throws QueueDriverException
     */
    public function index(MetricsService $service): JsonResponse
    {
        return new JsonResponse($service->getMetrics(), Response::HTTP_OK);
    }
}
