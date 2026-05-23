<?php
namespace RiskManager;

use Base\View\Helper\CurrentRequest;
use Laminas\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        CurrentRequest::setServiceManager($e->getApplication()->getServiceManager());
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

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'currentRequest' => 'Base\View\Helper\CurrentRequest',
                'formRowNoLabel' => 'Base\View\Helper\FormRowNoLabel',
                'RiskManager' => 'Base\View\Helper\RiskManager',
            ),
        );
    }
}
