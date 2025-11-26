<?php

return array(
    'db' => array(
        'username' => 'inep',
        'password' => '@adminb21#',
        'database'=>'inep',
        'host'=>'mysql524.umbler.com',
        'dsn'      => 'mysql:dbname=inep;host=mysql524.umbler.com;port=41890',
    ),
    'API' => [
        'baseSis' => 'http://192.168.2.12/', /// Endereço da aplicação
        'baseRM' => 'https://gestaoderiscos.inep.gov.br/', /// Endereço do RM
        'patchRM' => 'RM7', /// Base patch do RM
        'workFlowRM' => 'WF', /// Base patch do Workflow
        'idRM' => '9f0eed212bf54d17bd9b1e0ad7389793', /// Id da aplicação
        'secretRM' => '2a92f6c829f942179e75a19cc2adbdf5', /// Secret da aplicação

    ],
    'url_painel'=>'http://mapas-inep.local',
    'local_arquivo_configuracao'=>'C:\Users\rafael.marques\Projetos\inep_pro\public\sistema',
    'service_manager' => array(
        'factories' => array(
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'base_path' => '/'
    )
);