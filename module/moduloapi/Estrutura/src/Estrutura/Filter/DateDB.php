<?php

namespace Estrutura\Filter;

use Laminas\Filter\AbstractFilter;

class DateDB extends AbstractFilter
{
    public function filter($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = \DateTime::createFromFormat('d/m/Y', $value);
        if (!$date) {
            return null;
        }

        return $date->format('Y-m-d');
    }

}
