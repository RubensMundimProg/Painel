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
        'baseSis' => 'https://painel.gestaoderiscos.inep.gov.br/', /// Endereço da aplicação
        'baseRM' => 'https://gestaoderiscos.inep.gov.br/', /// Endereço do RM
        'patchRM' => 'RM', /// Base patch do RM
        'workFlowRM' => 'WF', /// Base patch do Workflow
        'idRM' => '665447d212614956ba04d2d7f682ab01', /// Id da aplicação
        'secretRM' => '0727110ceb4b46b785d06c8b8ab67164', /// Secret da aplicação

    ],
    'url_painel'=>'https://painel.gestaoderiscos.inep.gov.br/aplicacao',
    'local_arquivo_configuracao'=>'C:\inetpub\wwwroot\aplicacao\\',
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'base_path' => '/'
    )
);