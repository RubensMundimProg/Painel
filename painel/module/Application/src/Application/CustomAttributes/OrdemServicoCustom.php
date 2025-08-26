<?php

namespace Application\CustomAttributes;

use RiskManager\OData\CustomAttributes;

class OrdemServicoCustom extends CustomAttributes
{
    protected $attributes  = [
        'total_ust',
        'servico',
        'situacao',
        'valor_os',
        'data_de_inicio',
        'data_de_termino',
        'servico_s1',
        'servico_s2',
        'servico_s3',
        'servico_s4',
        'servico_s5',
        'servico_s6',
        'servico_s7',
        'servico_s8'
    ];
}