<?php

namespace Estrutura\Filter;

use Zend\Filter\AbstractFilter;

class DateDB extends AbstractFilter
{
    public function filter($value)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $value);
        return $date->format('Y-m-d');
    }

}
