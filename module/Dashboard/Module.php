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
