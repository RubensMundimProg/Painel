<?php

namespace Estrutura\Filter;

use RiskManager\OData\People;
use Laminas\Filter\AbstractFilter;

class PeopleRM extends AbstractFilter
{
    public function filter($value)
    {
        if($value == '') return '';
        if($value instanceof People) return $value;

        if(is_string($value)){
            $people = new People();
            $people->setPerson($value);
        }

        if(is_array($value)){
            $people = new People();
            foreach($value as $item){
                $people->setPerson($item);
            }
        }

        return isset($people) ? $people : '';
    }
}
