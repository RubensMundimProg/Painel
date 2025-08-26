<?php

return array(
    'modules' => array(
        'Application',
        'Dashboard',
        'Autenticacao',
        'Usuario',
        'Estrutura',
        'DOMPDFModule',
        'Gerador',
        'Modulo',
        'RiskManager',
        'Base',
        'Classes',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './vendor/moduloapi',
        ),
        'config_glob_paths' => array(
            'config/autoload/global.php',
        ),
    ),
);
