<?php

declare(strict_types=1);

use App\Handler\HealthHandler;
use Mezzio\Application;

return static function (Application $app): void {
    $app->get('/health', HealthHandler::class, 'health');
};
