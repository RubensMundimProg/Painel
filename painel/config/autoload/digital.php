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
        'idRM' => '665447d212614956ba04d2d7f682ab01', /// Id da aplicação
        'secretRM' => '0727110ceb4b46b785d06c8b8ab67164', /// Secret da aplicação

    ],
    'url_painel'=>'http://gestaoderiscos.inep.gov.br/aplicacao',
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