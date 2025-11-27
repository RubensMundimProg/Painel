<?php

namespace Dashboard;

use Api\Exception\ApiException;
use Estrutura\Form\AbstractForm;
use Estrutura\Service\AbstractEstruturaService;
use RiskManager\MySpace\Service\Me;
use RiskManager\OData\TokenDetails;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Validator\AbstractValidator;
use Laminas\I18n\Translator\Translator;
use Estrutura\Service\Config;
use Modulo\Service\RiskManager;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // --- CÓDIGO ADICIONADO PELO GUARDIÃO ---
        // Escuta o evento de despacho para trocar o layout baseado no módulo
        $e->getApplication()->getEventManager()->getSharedManager()->attach(
            'Laminas\Mvc\Controller\AbstractActionController', 
            'dispatch', 
            function($e) {
                $controller      = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
                $config          = $e->getApplication()->getServiceManager()->get('config');

                if (isset($config['module_layouts'][$moduleNamespace])) {
                    $controller->layout($config['module_layouts'][$moduleNamespace]);
                }
            }, 
            100
        );
        // ---------------------------------------
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}