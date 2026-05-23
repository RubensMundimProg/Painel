<?php

declare(strict_types=1);

use Mezzio\Application;

chdir(dirname(__DIR__));

date_default_timezone_set('America/Fortaleza');

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', $_SERVER['APPLICATION_ENV'] ?? getenv('APPLICATION_ENV') ?: 'production');
}

if (!defined('BASE_PATCH')) {
    define('BASE_PATCH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $_SERVER['HTTP_HOST'] ?? 'cli');
}

require 'vendor/autoload.php';

$container = require 'config/mezzio/container.php';
$app = $container->get(Application::class);

(require 'config/mezzio/routes.php')($app);
(require 'config/mezzio/pipeline.php')($app);
$app->run();
