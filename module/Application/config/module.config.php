<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Laminas\\Router\\Http\\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'dashboard' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        'controller' => 'dashboard-index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'error' => array(
                'type' => 'Laminas\\Router\\Http\\Literal',
                'options' => array(
                    'route'    => '/error/nao-autorizado',
                    'defaults' => array(
                        'controller' => 'error',
                        'action'     => 'nao-autorizado',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:controller[/:action[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',                                                
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Laminas\Cache\Service\StorageCacheAbstractServiceFactory',
        ),
        'factories' => array(
            \Dompdf\Dompdf::class => \Application\Factory\DompdfFactory::class,
            \Application\Service\PdfGenerator::class => \Application\Factory\PdfGeneratorFactory::class,
        ),
        'shared' => array(
            \Dompdf\Dompdf::class => false,
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'dompdf' => array(
        'temp_dir' => BASE_PATCH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'tmp',
        'font_dir' => BASE_PATCH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'fonts',
        'font_cache' => BASE_PATCH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'fonts',
        'chroot' => BASE_PATCH,
        'default_paper_size' => 'A4',
        'default_font' => 'Arial',
        'dpi' => 96,
        'is_remote_enabled' => true,
        'is_html5_parser_enabled' => true,
        'is_font_subsetting_enabled' => false,
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
            'index' => 'Application\Controller\IndexController',
            'triagem' => 'Application\Controller\TriagemController',
            'validados' => 'Application\Controller\ValidadosController',
            'relatorios' => 'Application\Controller\RelatoriosController',
            'error' => 'Application\Controller\ErrorController',
            'configuracao' => 'Application\Controller\ConfiguracaoController',
            'api' => 'Application\Controller\ApiController',
            'ajuda' => 'Application\Controller\AjudaController',
            'calendario' => 'Application\Controller\CalendarioController',
            'gantt' => 'Application\Controller\GanttController',
            'contratos' => 'Application\Controller\ContratosController',
            'backup' => 'Application\Controller\BackupController',
        ),
    ),
    'controller_plugins' => array(
        'aliases' => array(
            'flashmessenger' => \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger::class,
            'flashMessenger' => \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger::class,
            'FlashMessenger' => \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger::class,
        ),
        'factories' => array(
            \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
        ),
    ),
    'view_helpers' => array(
        'aliases' => array(
            'flashmessenger' => \Laminas\View\Helper\FlashMessenger::class,
            'flashMessenger' => \Laminas\View\Helper\FlashMessenger::class,
            'FlashMessenger' => \Laminas\View\Helper\FlashMessenger::class,
        ),
        'factories' => array(
            \Laminas\View\Helper\FlashMessenger::class => \Laminas\View\Helper\Service\FlashMessengerFactory::class,
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'menu'          => __DIR__ . '/../view/layout/menu.phtml',
            'layout/modern'          => __DIR__ . '/../view/layout/modern.phtml',
            'layout/kanban'          => __DIR__ . '/../view/layout/kanban.phtml',
            'layout/clean'          => __DIR__ . '/../view/layout/clean.phtml',
            'layout/clean-rm'          => __DIR__ . '/../view/layout/clean-rm.phtml',
            'layout/ajuda'          => __DIR__ . '/../view/layout/ajuda.phtml',
            'layout/vazio'          => __DIR__ . '/../view/layout/limpo.phtml',
            'modal-detalhes-evento'          => __DIR__ . '/../view/application/modals/modal-detalhes-evento.phtml',
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
                'dashboard' => array(
                    'options' => array(
                        'route'    => 'dashboard',
                        'defaults' => array(
                            'controller' => 'dashboard-data',
                            'action'     => 'save-data'
                        )
                    )
                ),
                'build-dashboard' => array(
                    'options' => array(
                        'route'    => 'build-dashboard',
                        'defaults' => array(
                            'controller' => 'dashboard',
                            'action'     => 'build-dashboard-data'
                        )
                    )
                ),
                'twitter' => array(
                    'options' => array(
                        'route'    => 'twitter',
                        'defaults' => array(
                            'controller' => 'twitter',
                            'action'     => 'streaming'
                        )
                    )
                ),
                'restart-twitter' => array(
                    'options' => array(
                        'route'    => 'restart-twitter',
                        'defaults' => array(
                            'controller' => 'twitter',
                            'action'     => 'restart-streaming'
                        )
                    )
                ),
                'rss' => array(
                    'options' => array(
                        'route'    => 'rss',
                        'defaults' => array(
                            'controller' => 'rss',
                            'action'     => 'load-rss'
                        )
                    )
                ),
                'kml-ultima-milha' => array(
                    'options' => array(
                        'route'    => 'kml-ultima-milha',
                        'defaults' => array(
                            'controller' => 'dashboard',
                            'action'     => 'processa-kml-ultima-milha'
                        )
                    )
                ),
                'geojson' => array(
                    'options' => array(
                        'route'    => 'geojson',
                        'defaults' => array(
                            'controller' => 'dashboard',
                            'action'     => 'build-geojson-aplicadores'
                        )
                    )
                ),
                'cache-validados' => array(
                    'options' => array(
                        'route'    => 'cache-validados',
                        'defaults' => array(
                            'controller' => 'validados',
                            'action'     => 'gerar-cache'
                        )
                    )
                ),
                'dados-api' => array(
                    'options' => array(
                        'route'    => 'dados-api',
                        'defaults' => array(
                            'controller' => 'api',
                            'action'     => 'tratar-dados-json'
                        )
                    )
                ),
                'limpar-duplicados' => array(
                    'options' => array(
                        'route'    => 'limpar-duplicados',
                        'defaults' => array(
                            'controller' => 'triagem',
                            'action'     => 'limpar-duplicados'
                        )
                    )
                ),
                'limpar-acesso' => array(
                    'options' => array(
                        'route'    => 'limpar-acesso',
                        'defaults' => array(
                            'controller' => 'index',
                            'action'     => 'limpar-acesso'
                        )
                    )
                ),
                'consolidado'=> array(
                    'options' => array(
                        'route'    => 'consolidado',
                        'defaults' => array(
                            'controller' => 'index',
                            'action'     => 'consolidado'
                        )
                    )
                ),
                'calendario-gantt' => array(
                    'options' => array(
                        'route'    => 'calendario-gantt',
                        'defaults' => array(
                            'controller' => 'calendario',
                            'action'     => 'load-events'
                        )
                    )
                ),
                'cache-pre' => array(
                    'options' => array(
                        'route'    => 'cache-pre',
                        'defaults' => array(
                            'controller' => 'dashboard',
                            'action'     => 'alertas-pre'
                        )
                    )
                ),
                'atualiza-label-ativos' => array(
                    'options' => array(
                        'route'    => 'atualiza-label-ativos',
                        'defaults' => array(
                            'controller' => 'dashboard',
                            'action'     => 'atualiza-label-ativos'
                        )
                    )
                ),
                'update-exame' => array(
                    'options' => array(
                        'route'    => 'update-exame',
                        'defaults' => array(
                            'controller' => 'triagem',
                            'action'     => 'update-exame'
                        )
                    )
                ),
                'backup-ocorrencias' => array(
                    'options' => array(
                        'route'    => 'backup-ocorrencias',
                        'defaults' => array(
                            'controller' => 'backup',
                            'action'     => 'start'
                        )
                    )
                ),
            ),
        ),
    ),
);
