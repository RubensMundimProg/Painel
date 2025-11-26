<?php
///Configuração de Acesso

return array(
    'console'=>[
      'dashboard-data'
    ],
    'rmadm' => [
        '*'
    ],
    'ciccn' => [
        'index' => [
            'index',
        ],
        'triagem' => [
            'index',
            'confirmar-triagem',
            'excluir-triagem',
        ],
        'validados' => [
            'index',
        ],
        'ultima-milha' => [
            'index',
        ],
        'dashboard' => [
            'index'
        ],
        'mapas' => [
            'index'
        ],
        'dashboard-index' => [
            '*',
        ],
        'dashboard-data' => [
            '*',
        ],
    ],
    'ciccr' => [
        'index' => [
            'index',
        ],
        'triagem' => [
            'cadastro-alerta',
            'filtrar-municipio'
        ],
        'validados' => [
            'index',
        ],
        'ultima-milha' => [
            'index',
        ],
        'dashboard' => [
            'index'
        ],
        'mapas' => [
            'index'
        ],
        'dashboard-index' => [
            '*',
        ],
        'dashboard-data' => [
            '*',
        ],
    ],
    'operacional' => [
        'index' => [
            'index',
        ],
        'triagem' => [
            'cadastro-alerta',
            'filtrar-municipio'
        ],
        'validados' => [
            'index',
        ],
        'ultima-milha' => [
            'index',
        ],
        'dashboard' => [
            'index'
        ],
        'mapas' => [
            'index'
        ],
        'dashboard-index' => [
            '*',
        ],
        'dashboard-data' => [
            '*',
        ],
    ],
    'rnc' => [
        'index' => [
            'index',
        ],
        'triagem' => [
            'index',
            'cadastro-alerta',
            'confirmar-triagem',
            'excluir-triagem',
            'filtrar-municipio',
        ],
        'validados' => [
            'index',
            'atualizar'
        ],
        'ultima-milha' => [
            'index',
        ],
        'dashboard' => [
            'index'
        ],
        'mapas' => [
            'index'
        ],
        'dashboard-index' => [
            '*',
        ],
        'dashboard-data' => [
            '*',
        ],
    ],
    'dashboard' => [
        'index' => [
            'index',
        ],
        'dashboard' => [
            'index'
        ],
        'dashboard-index' => [
            '*',
        ],
        'dashboard-data' => [
            '*',
        ],
        'twitter' => [
            '*',
        ],
    ],
);