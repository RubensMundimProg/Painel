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
        'idRM' => '665447d212614956ba04d2d7f682ab01', /// Id da aplicação
        'secretRM' => '0727110ceb4b46b785d06c8b8ab67164', /// Secret da aplicação

    ],
    'url_painel'=>'http://mapas-inep.local',
    'local_arquivo_configuracao'=>'C:\Users\rafael.marques\Projetos\inep_pro\public\sistema',
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'view_manager' => array(
        'base_path' => '/'
    )
);