<?php
namespace Estrutura;

use Estrutura\Form\AbstractForm;
use Estrutura\Service\AbstractEstruturaService;
use Usuario\Service\Usuario;
use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $e->getApplication()->getEventManager()->getSharedManager()->attach('Laminas\Mvc\Controller\AbstractActionController', 'dispatch', function($e)
            {
                $controller = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
                $config = $e->getApplication()->getServiceManager()->get('config');
                if (isset($config['module_layouts'][$moduleNamespace])) {
                    $controller->layout($config['module_layouts'][$moduleNamespace]);
                }
            }
            , 100);
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
            'invokables' => array (
                'formataCPFouCNPJ' => '\Estrutura\View\Helper\FormataCPFouCNPJ($cpf)',
                'Usuario'=> '\Estrutura\View\Helper\Usuario',
                'Projeto'=> '\Estrutura\View\Helper\Projeto',
                'Logo'=> '\Estrutura\View\Helper\Logo',
                'Data'=> '\Estrutura\View\Helper\Data',
                'FormInput' => '\Estrutura\View\Helper\FormInput',
                'Perfil' => '\Estrutura\View\Helper\Perfil',
                'Info' => '\Estrutura\View\Helper\Info',
                'Acl' => '\Estrutura\View\Helper\Acl',
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Usuario' => function($sm){
                        $container = new Container('Usuario');
                        $id = $container->offsetGet('id');
                        if(!$id) return false;

                        $objUsuario = new Usuario();
                        $usuario = $objUsuario->buscar($id);
                        return $usuario;
                    }
            ),
        );
    }
}
