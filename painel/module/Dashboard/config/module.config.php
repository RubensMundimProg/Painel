<?php
return array(
    'router' => array(
        'routes' => array(
            'dashboard' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        'controller' => 'dashboard',
                        'action'     => 'index',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'twitter-page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/:controller[/:action]',
                    'defaults' => array(
                        'controller' => 'twitter',
                        'action'     => 'index',
                    ),
                ),
            ),
            'error' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/error/nao-autorizado',
                    'defaults' => array(
                        'controller' => 'error',
                        'action'     => 'nao-autorizado',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'pt_BR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'module_layouts' => array(
        'Dashboard' => 'layout/dashboard',
    ),
    'controllers' => array(
        'invokables' => array(
            'dashboard' => 'Dashboard\Controller\DashboardController',
            'dashboard-index' => 'Dashboard\Controller\IndexController',
            'dashboard-data' => 'Dashboard\Controller\ProcessDataController',
            'twitter' => 'Dashboard\Controller\TwitterController',
            'error' => 'Dashboard\Controller\ErrorController',
            'rss' => 'Dashboard\Controller\RssController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
//            'layout/dashboard'          => __DIR__ . '/../view/layout/dashboard.phtml',
            'layout/dashboard'          => __DIR__ . '/../view/layout/layout.phtml',
            'header/dashboard'          => __DIR__ . '/../view/layout/header.phtml',
            'footer/dashboard'          => __DIR__ . '/../view/layout/footer.phtml',
            'detalhes-lateral-direita/dashboard'          => __DIR__ . '/../view/layout/detalhes-lateral-direita.phtml',
            'layout/dashboard/clean'          => __DIR__ . '/../view/layout/dashboard-clean.phtml',
            'abas/dashboard'          => __DIR__ . '/../view/layout/abas-dashboard.phtml',
            'pag-aplicacao-a'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-a.phtml',
            'pag-aplicacao-dia'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-dia.phtml',
            'pag-aplicacao-b'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-b.phtml',
            'pag-aplicacao-c'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-c.phtml',
            'pag-aplicacao-d'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-d.phtml',
            'pag-aplicacao-e'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-e.phtml',
            'pag-aplicacao-f'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-f.phtml',
            'pag-aplicacao-g'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-g.phtml',
            'pag-aplicacao-h'          => __DIR__ . '/../view/dashboard/dashboard/pag-aplicacao-h.phtml',
            'pag-alertas'          => __DIR__ . '/../view/dashboard/dashboard/pag-alertas.phtml',
            'pag-estados'          => __DIR__ . '/../view/dashboard/dashboard/pag-estados.phtml',
            'pag-mapa'          => __DIR__ . '/../view/dashboard/dashboard/pag-mapa.phtml',
            'pag-midia'          => __DIR__ . '/../view/dashboard/dashboard/pag-midia.phtml',
            'pag-ultima-milha'          => __DIR__ . '/../view/dashboard/dashboard/pag-ultima-milha.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
