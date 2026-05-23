<?php

namespace Estrutura\Filter;

use Laminas\Filter\AbstractFilter;

class Decimal extends AbstractFilter
{
    public function filter($value)
    {
        return intval($value);
    }
}
