<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HealthHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'status' => 'ok',
            'application' => 'painel',
            'runtime' => 'mezzio',
            'php' => PHP_VERSION,
            'env' => defined('APPLICATION_ENV') ? APPLICATION_ENV : null,
            'time' => date('c'),
        ]);
    }
}
