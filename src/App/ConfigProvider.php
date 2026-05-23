<?php

declare(strict_types=1);

namespace App;

use App\Handler\HealthHandler;
use Laminas\ServiceManager\Factory\InvokableFactory;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'factories' => [
                HealthHandler::class => InvokableFactory::class,
            ],
        ];
    }
}
