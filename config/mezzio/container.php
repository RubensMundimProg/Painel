<?php

declare(strict_types=1);

use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceManager;

$providers = [
    Laminas\Diactoros\ConfigProvider::class,
    Mezzio\ConfigProvider::class,
    Mezzio\Router\ConfigProvider::class,
    Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    App\ConfigProvider::class,
];

$mergeConfig = static function (array $base, array $override) use (&$mergeConfig): array {
    foreach ($override as $key => $value) {
        if (isset($base[$key]) && is_array($base[$key]) && is_array($value)) {
            $base[$key] = $mergeConfig($base[$key], $value);
            continue;
        }

        $base[$key] = $value;
    }

    return $base;
};

$config = [];
foreach ($providers as $provider) {
    $config = $mergeConfig($config, (new $provider())());
}

$container = new ServiceManager();
(new Config($config['dependencies'] ?? []))->configureServiceManager($container);
$container->setService('config', $config);

return $container;
