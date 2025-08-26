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
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Zend\Cache\Storage\Filesystem' => function($sm) {
                $cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem',
                        'options' => array(
                            // tempo de validade do cache
                            'ttl' => 1000, // 5 min
                            // adicionando o diretorio data/cache para salvar os caches.
                            'cacheDir' => './data/cache'
                        ),
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => false),
                        'Serializer'
                    )
                ));

                return $cache;
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
