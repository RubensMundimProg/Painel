<?php

namespace Dashboard;

use Api\Exception\ApiException;
use Estrutura\Form\AbstractForm;
use Estrutura\Service\AbstractEstruturaService;
use RiskManager\MySpace\Service\Me;
use RiskManager\OData\TokenDetails;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\I18n\Translator\Translator;
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
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}