<?php

return array(
    'router' => array(
        'routes' => array(
            'classes-home' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/classes',
                    'defaults' => array(
                        'controller' => 'classes',
                        'action'     => 'index',
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
        'locale' => 'en_US',
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
//            'classes' => 'Classes\Controller\ClassesController',
//            'classes-blacklist' => 'Classes\Controller\BlacklistController',
//            'classes-nacionalidade' => 'Classes\Controller\NacionalidadeController',
//            'classes-pais' => 'Classes\Controller\PaisController',
//            'classes-tipo-documento' => 'Classes\Controller\TipoDocumentoController',

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
