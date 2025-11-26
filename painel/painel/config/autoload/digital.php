<?php

return array(
    'db' => array(
        'username' => 'root',
        'password' => 'QrSF6OxzNK',
        'database'=>'INEP_PROD',
        'host'=>'localhost',
        'dsn'      => 'mysql:dbname=INEP_PROD;host=localhost',
    ),
    'API' => [
        'baseSis' => 'http://painelgestaoderiscos.com.br/', /// Endereço da aplicação
        'baseRM' => 'https://gestaoderiscos.inep.gov.br/', /// Endereço do RM
        'patchRM' => 'RM7', /// Base patch do RM
        'workFlowRM' => 'WF', /// Base patch do Workflow
        'idRM' => '9f0eed212bf54d17bd9b1e0ad7389793', /// Id da aplicação
        'secretRM' => '2a92f6c829f942179e75a19cc2adbdf5', /// Secret da aplicação

    ],
    'url_painel'=>'http://gestaoderiscos.inep.gov.br/aplicacao',
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