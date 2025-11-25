<?php

namespace Modulo;

use Zend\Session\Container;

class Module
{
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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'UsuarioApi' => function($sm){
                        $container = new Container('UsuarioApi');
                        $id = $container->offsetGet('id');
                        if(!$id) return false;
                        return true;
                    }
            ),
        );
    }
}
