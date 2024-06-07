<?php

use Saritasa\LaravelMetrics\Http\Controllers\MetricsController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::prefix('metrics')
    ->group(function (Router $router): void {
        $router->get('', MetricsController::class . '@index');
    });
