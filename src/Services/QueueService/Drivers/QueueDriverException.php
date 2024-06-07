<?php

namespace Saritasa\LaravelMetrics\Services\QueueService\Drivers;

use Exception;
use Throwable;

class QueueDriverException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Incorrect connection for driver.';
        }

        parent::__construct($message, $code, $previous);
    }
}
