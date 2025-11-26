<?php

return array(
    'db' => array(
        'username' => 'root',
        'password' => 'adminb21',
        'database'=>'inep',
        'host'=>'locahost',
        'dsn'      => 'mysql:dbname=inep;host=localhost',
    ),
//    'db' => array(
//        'username' => 'root',
//        'password' => 'QrSF6OxzNK',
//        'database'=>'INEP_PROD',
//        'host'=>'locahost',
//        'dsn'      => 'mysql:dbname=INEP_PROD;host=159.203.98.205',
//    ),
    'API' => [
        'baseSis' => 'http://painel-inep.local/', /// Endereço da aplicação
        'baseRM' => 'https://gestaoderiscos.inep.gov.br/', /// Endereço do RM
        'patchRM' => 'RM', /// Base patch do RM
        'workFlowRM' => 'WF', /// Base patch do Workflow
        'idRM' => '9f0eed212bf54d17bd9b1e0ad7389793', /// Id da aplicação
        'secretRM' => '2a92f6c829f942179e75a19cc2adbdf5', /// Secret da aplicação

    ],
    'url_painel'=>'http://mapas-inep.local',
    'local_arquivo_configuracao'=>'C:\www\inepProducao\painel\data',
    'service_manager' => array(
        'factories' => array(
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'base_path' => '/'
    )
);