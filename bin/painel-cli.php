<?php

use Application\View\Helper\CurrentRequest as ApplicationCurrentRequest;
use Base\View\Helper\CurrentRequest as BaseCurrentRequest;
use Estrutura\Controller\AbstractEstruturaController;
use Estrutura\Form\AbstractForm;
use Estrutura\Service\AbstractEstruturaService;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\Parameters;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;

error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('America/Fortaleza');

$root = dirname(__DIR__);
chdir($root);

define('APPLICATION_ENV', $_SERVER['APPLICATION_ENV'] ?? getenv('APPLICATION_ENV') ?: 'production');
define('BASE_PATCH', $root);
define('BASE_URL', $_SERVER['HTTP_HOST'] ?? 'cli');

$command = $argv[1] ?? null;
define('PAINEL_CLI', true);
define('PAINEL_CLI_COMMAND', (string) ($command ?? ''));
$checkOnly = in_array($command, ['--check', 'check'], true);

$startedAt = microtime(true);
$logFile = $root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'cli.log';
$finished = false;
$exitCode = 0;
$outputStarted = false;

$staticCommands = [
    'dashboard' => ['controller' => 'dashboard-data', 'action' => 'save-data'],
    'build-dashboard' => ['controller' => 'dashboard', 'action' => 'build-dashboard-data'],
    'twitter' => ['controller' => 'twitter', 'action' => 'streaming'],
    'restart-twitter' => ['controller' => 'twitter', 'action' => 'restart-streaming'],
    'rss' => ['controller' => 'rss', 'action' => 'load-rss'],
    'kml-ultima-milha' => ['controller' => 'dashboard', 'action' => 'processa-kml-ultima-milha'],
    'geojson' => ['controller' => 'dashboard', 'action' => 'build-geojson-aplicadores'],
    'cache-validados' => ['controller' => 'validados', 'action' => 'gerar-cache'],
    'dados-api' => ['controller' => 'api', 'action' => 'tratar-dados-json'],
    'limpar-duplicados' => ['controller' => 'triagem', 'action' => 'limpar-duplicados'],
    'limpar-acesso' => ['controller' => 'index', 'action' => 'limpar-acesso'],
    'consolidado' => ['controller' => 'index', 'action' => 'consolidado'],
    'calendario-gantt' => ['controller' => 'calendario', 'action' => 'load-events'],
    'cache-pre' => ['controller' => 'dashboard', 'action' => 'alertas-pre'],
    'atualiza-label-ativos' => ['controller' => 'dashboard', 'action' => 'atualiza-label-ativos'],
    'atualizar-label-ativos' => ['controller' => 'dashboard', 'action' => 'atualiza-label-ativos'],
    'update-exame' => ['controller' => 'triagem', 'action' => 'update-exame'],
    'backup-ocorrencias' => ['controller' => 'backup', 'action' => 'start'],
];

$log = static function (string $level, string $message, array $context = []) use ($logFile, $command): void {
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $line = '[' . date('Y-m-d H:i:s') . ']'
        . ' [' . strtoupper($level) . ']'
        . ' [' . ($command ?: '-') . '] '
        . $message;

    if ($context) {
        $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    file_put_contents($logFile, $line . PHP_EOL, FILE_APPEND);
};

register_shutdown_function(static function () use (&$finished, &$exitCode, &$outputStarted, $startedAt, $log): void {
    if ($finished) {
        return;
    }

    $error = error_get_last();
    $output = $outputStarted ? (string) ob_get_contents() : '';
    $duration = round(microtime(true) - $startedAt, 3);

    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $exitCode = 1;
        $log('error', 'Comando finalizado com erro fatal.', [
            'duration_seconds' => $duration,
            'error' => $error,
            'output' => trim($output),
        ]);
        return;
    }

    $log('info', 'Comando finalizado.', [
        'duration_seconds' => $duration,
        'exit_code' => $exitCode,
        'output' => trim($output),
    ]);
});

set_error_handler(static function (int $severity, string $message, string $file, int $line) use ($log): bool {
    $log('warning', $message, ['file' => $file, 'line' => $line, 'severity' => $severity]);
    return false;
});

try {
    if ($command === null || in_array($command, ['-h', '--help', 'help'], true)) {
        echo usage($staticCommands);
        $finished = true;
        exit($command === null ? 1 : 0);
    }

    $autoload = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('vendor/autoload.php nao encontrado. Execute composer install/update antes.');
    }
    require $autoload;

    $appConfigFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'application.config.php';
    if (!is_file($appConfigFile)) {
        throw new RuntimeException('config/application.config.php nao encontrado.');
    }

    $appConfig = require $appConfigFile;
    $application = Application::init($appConfig);
    $serviceManager = $application->getServiceManager();
    registerLegacyServiceManager($serviceManager);

    $config = $serviceManager->get('config');
    $commands = array_replace($staticCommands, commandsFromConfig($config));

    if ($checkOnly) {
        echo checkCommands($commands, $serviceManager);
        $finished = true;
        exit(0);
    }

    if (!isset($commands[$command])) {
        throw new InvalidArgumentException("Comando desconhecido: {$command}");
    }

    $defaults = $commands[$command];
    $controllerAlias = $defaults['controller'] ?? null;
    $action = $defaults['action'] ?? null;

    if (!$controllerAlias || !$action) {
        throw new RuntimeException("Comando {$command} sem controller/action configurado.");
    }

    $log('info', 'Comando iniciado.', [
        'controller' => $controllerAlias,
        'action' => $action,
        'env' => APPLICATION_ENV,
    ]);

    $controllerManager = $serviceManager->get('ControllerManager');
    if (!$controllerManager->has($controllerAlias)) {
        throw new RuntimeException("Controller alias nao encontrado: {$controllerAlias}");
    }

    $controller = $controllerManager->get($controllerAlias);
    $query = [];

    if ($command === 'cache-validados') {
        $query['once'] = '1';
    }

    $request = new Request();
    $request->setMethod(Request::METHOD_GET);
    $request->setUri('/' . $command);
    $request->setQuery(new Parameters($query));
    $request->setPost(new Parameters([]));

    $response = new Response();
    $routeMatch = new RouteMatch([
        'controller' => $controllerAlias,
        'action' => $action,
    ]);
    $routeMatch->setMatchedRouteName($command);

    $event = new MvcEvent();
    $event->setApplication($application);
    $event->setRequest($request);
    $event->setResponse($response);
    $event->setRouteMatch($routeMatch);
    $event->setParam('application', $application);

    if (method_exists($controller, 'setEvent')) {
        $controller->setEvent($event);
    }

    ob_start();
    $outputStarted = true;

    $actionMethod = actionToMethod($action);
    if (!method_exists($controller, $actionMethod)) {
        throw new RuntimeException(
            sprintf('Action nao encontrada: %s::%s', get_class($controller), $actionMethod)
        );
    }

    if ($command === 'build-dashboard') {
        $result = $controller->{$actionMethod}(null, null, false);
    } else {
        $result = $controller->{$actionMethod}();
    }

    $output = (string) ob_get_clean();
    $outputStarted = false;

    $resultText = resultToText($result);
    if ($output !== '') {
        echo $output;
    }
    if ($resultText !== '') {
        echo $resultText;
        if (!str_ends_with($resultText, PHP_EOL)) {
            echo PHP_EOL;
        }
    }

    if ($result instanceof JsonModel && (bool) $result->getVariable('error', false)) {
        $exitCode = 1;
        $log('error', 'Comando retornou erro.', [
            'duration_seconds' => round(microtime(true) - $startedAt, 3),
            'result' => $result->getVariables(),
            'output' => trim($output),
        ]);
        $finished = true;
        exit(1);
    }

    $log('info', 'Comando finalizado com sucesso.', [
        'duration_seconds' => round(microtime(true) - $startedAt, 3),
        'result' => shortResult($result),
        'output' => trim($output),
    ]);

    $finished = true;
    exit(0);
} catch (Throwable $e) {
    $exitCode = 1;
    if ($outputStarted) {
        $output = (string) ob_get_clean();
        $outputStarted = false;
    } else {
        $output = '';
    }

    $log('error', $e->getMessage(), [
        'duration_seconds' => round(microtime(true) - $startedAt, 3),
        'exception' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'output' => trim($output),
    ]);

    if ($output !== '') {
        echo $output;
    }

    fwrite(STDERR, 'Erro: ' . $e->getMessage() . PHP_EOL);
    $finished = true;
    exit(1);
}

function commandsFromConfig(array $config): array
{
    $commands = [];
    $routes = $config['console']['router']['routes'] ?? [];

    foreach ($routes as $name => $route) {
        $defaults = $route['options']['defaults'] ?? [];
        if (isset($defaults['controller'], $defaults['action'])) {
            $commands[$name] = [
                'controller' => $defaults['controller'],
                'action' => $defaults['action'],
            ];
        }
    }

    return $commands;
}

function registerLegacyServiceManager($serviceManager): void
{
    $targets = [
        AbstractEstruturaService::class,
        AbstractForm::class,
        AbstractEstruturaController::class,
        ApplicationCurrentRequest::class,
        BaseCurrentRequest::class,
    ];

    foreach ($targets as $class) {
        if (class_exists($class) && method_exists($class, 'setServiceManager')) {
            $class::setServiceManager($serviceManager);
        }
    }
}

function actionToMethod(string $action): string
{
    $method = str_replace(['-', '_', '.'], ' ', $action);
    $method = str_replace(' ', '', ucwords($method));

    return lcfirst($method) . 'Action';
}

function resultToText($result): string
{
    if ($result instanceof JsonModel) {
        $variables = $result->getVariables();
        $text = '';

        if (isset($variables['dados']['log']) && is_string($variables['dados']['log'])) {
            $text .= $variables['dados']['log'];
        }

        if (isset($variables['message']) && $variables['message'] !== '') {
            $text .= (str_ends_with($text, PHP_EOL) || $text === '' ? '' : PHP_EOL) . $variables['message'] . PHP_EOL;
        }

        return $text;
    }

    if ($result instanceof ModelInterface) {
        return '';
    }

    if (is_scalar($result)) {
        return (string) $result;
    }

    return '';
}

function shortResult($result): string
{
    if ($result instanceof JsonModel) {
        return 'JsonModel';
    }

    if ($result instanceof ModelInterface) {
        return get_class($result);
    }

    if (is_object($result)) {
        return get_class($result);
    }

    return gettype($result);
}

function checkCommands(array $commands, $serviceManager): string
{
    ksort($commands);
    $controllerManager = $serviceManager->get('ControllerManager');
    $lines = ['Verificacao dos comandos CLI:'];

    foreach ($commands as $name => $defaults) {
        $controllerAlias = $defaults['controller'] ?? null;
        $action = $defaults['action'] ?? null;

        if (!$controllerAlias || !$action) {
            $lines[] = sprintf('  [ERRO] %-22s sem controller/action', $name);
            continue;
        }

        if (!$controllerManager->has($controllerAlias)) {
            $lines[] = sprintf('  [ERRO] %-22s controller nao encontrado: %s', $name, $controllerAlias);
            continue;
        }

        $controller = $controllerManager->get($controllerAlias);
        $method = actionToMethod($action);
        if (!method_exists($controller, $method)) {
            $lines[] = sprintf('  [ERRO] %-22s action nao encontrada: %s::%s', $name, get_class($controller), $method);
            continue;
        }

        $lines[] = sprintf('  [OK]   %-22s %s::%s', $name, $controllerAlias, $action);
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

function usage(array $commands): string
{
    ksort($commands);

    $lines = [
        'Uso:',
        '  php bin/painel-cli.php <comando>',
        '  php bin/painel-cli.php --check',
        '',
        'Comandos:',
    ];

    foreach ($commands as $name => $defaults) {
        $lines[] = sprintf('  %-22s %s::%s', $name, $defaults['controller'], $defaults['action']);
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}
