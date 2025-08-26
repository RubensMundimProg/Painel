<?php

namespace Application\CustomAttributes;

use RiskManager\OData\CustomAttributes;

class ContratoCustom extends CustomAttributes
{
    protected $attributes  = [
        'total_ust',
        'ust_utilizadas'
    ];
}