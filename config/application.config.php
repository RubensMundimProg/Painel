<?php

return array(
    'modules' => array(
        'Laminas\Router',
        'Laminas\Form',
        'Laminas\I18n',
        'Laminas\Cache',
        'Laminas\Cache\Storage\Adapter\Filesystem',
        'Laminas\Mvc\Plugin\FlashMessenger',
        'Application',
        'Dashboard',
        'Autenticacao',
        'Usuario',
        'Estrutura',
        'Gerador',
        'Modulo',
        'RiskManager',
        'Base',
        'Mobile',
        'Classes',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './module/moduloapi',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/global.php',
        ),
    ),
);
