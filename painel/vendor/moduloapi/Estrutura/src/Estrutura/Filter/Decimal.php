<?php

namespace Estrutura\Filter;

use Zend\Filter\AbstractFilter;

class Decimal extends AbstractFilter
{
    public function filter($value)
    {
        return intval($value);
    }
}
