<?php

return array(
    'db' => array(
        'username' => 'root',
        'password' => 'Drpncdj2547#',
        'database'=>'inep',
        'host'=>'localhost',
        'hostip' => '127.0.0.1',
        'dsn'      => 'mysql:dbname=inep;host=localhost',
        'bkp_dir'      => "c:\bkp\/",
        'expire_days'      => '32', //realizando 4 backups por dia 6/6h
    ),
    'API' => [
        'baseSis' => 'https://172.29.3.107/', /// Endereço da aplicação
        'baseRM' => 'https://gestaoderiscos.inep.gov.br/', /// Endereço do RM
        'patchRM' => 'RM', /// Base patch do RM
        'workFlowRM' => 'WF', /// Base patch do Workflow
        'idRM' => '9f0eed212bf54d17bd9b1e0ad7389793', /// Id da aplicação
        'secretRM' => '2a92f6c829f942179e75a19cc2adbdf5', /// Secret da aplicação

    ],

    'API_MOBILE' => [
        'client_id'     => '9f0eed212bf54d17bd9b1e0ad7389793',
        'client_secret' => '2a92f6c829f942179e75a19cc2adbdf5',
        'redirect_uri'  => 'https://172.29.3.107/callmobileback',
    ],
    'url_painel'=>'https://172.29.3.107/aplicacao',
    'local_arquivo_configuracao'=>'C:\inetpub\wwwroot\aplicacao\\',
    'service_manager' => array(
        'factories' => array(
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'base_path' => '/'
    )
);