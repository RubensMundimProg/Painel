<?php

return array(
    'router' => array(
        'routes' => array(
            'gerador-home' => array(
                'type' => 'Laminas\\Router\\Http\\Literal',
                'options' => array(
                    'route'    => '/gerador/',
                    'defaults' => array(
                        'controller' => 'gerador',
                        'action'     => 'index',
                    ),
                ),
            ),
            'gerador-seg' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/gerador/:action',
                    'defaults' => array(
                        'controller'    => 'gerador',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Laminas\Cache\Service\StorageCacheAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'gerador' => 'Gerador\Controller\GeradorController'
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
                'gerador' => array(
                    'type'    => 'simple',       // <- simple route is created by default, we can skip that
                    'options' => array(
                    'route'    => 'play',
                    'defaults' => array(
                        'controller' => 'gerador',
                        'action'     => 'play'
                    )
                  )
                )
            ),
        ),
    ),
);
