<?php

return [
    'db' => array(
        'driver' => 'Pdo',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'nomeProjeto' => 'Módulo Risk Manager',
    'general' => [
        'arquivos' => BASE_PATCH . DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'arquivos'.DIRECTORY_SEPARATOR,
    ],
    'VEFIFICA_ACL'=>true,
    'service_manager' => array(
        'aliases' => array(
            'MvcTranslator' => \Laminas\I18n\Translator\TranslatorInterface::class,
        ),
        'factories' => array(
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
            'Laminas\Cache\Storage\Filesystem' => function($sm) {
                $factory = $sm->get(\Laminas\Cache\Service\StorageAdapterFactoryInterface::class);

                return $factory->createFromArrayConfiguration(array(
                    'name' => 'filesystem',
                    'options' => array(
                        // tempo de validade do cache
                        'ttl' => 1000, // 5 min
                        // adicionando o diretorio data/cache para salvar os caches.
                        'cache_dir' => './data/cache'
                    ),
                    'plugins' => array(
                        array(
                            'name' => 'exception_handler',
                            'options' => array('throw_exceptions' => false),
                        ),
                        array('name' => 'serializer'),
                    )
                ));
            },
        ),
    ),
    'twitter' => array(
            'oauth_access_token' =>         "2805292805-pezle02XUlxHSPCOzh7fCV5wI8aINBBvK6WTsFs",
            'oauth_access_token_secret' =>  "FkkDSIQ7gNcqIM23FHNnz5R26Y0g6JA1xAZfve57wgLak",
            'consumer_key' => "8nZqYXVBCpKskrZnlQB0caVT4",
            'consumer_secret' => "2DXyRzgCuoZ4AAH0P2xE6ZWLR0NwYwUlxcToqPVMNfXH86TRAd"
    )
];
