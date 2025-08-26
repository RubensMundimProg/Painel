<?php

return array(
    'router' => array(
        'routes' => array(
            'modulo-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/modulo/',
                    'defaults' => array(
                        'controller' => 'Modulo',
                        'action'     => 'index',
                    ),
                ),
            ),
            'modulo-callback' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/callback[/:token]',
                    'defaults' => array(
                        'controller' => 'Callback',
                        'action'     => 'index',
                    ),
                ),
            ),
            'autenticar' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/autenticar',
                    'defaults' => array(
                        'controller' => 'Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'modulo-auth' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/auth[/:action]',
                    'defaults' => array(
                        'controller' => 'Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            'modulo' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/modulo',
                    'defaults' => array(
                        'controller'    => 'Modulo',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/modulo/[/:action[/:id]]',
                            'constraints' => array(
                            ),
                            'defaults' => array(
                                'controller'    => 'Modulo',
                                'action'        => 'index',
                            ),
                        ),
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
    'controllers' => array(
        'invokables' => array(
            'Modulo' => 'Modulo\Controller\ModuloController',
            'Auth'=> 'Modulo\Controller\AuthController',
            'Callback'=> 'Modulo\Controller\CallbackController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
