<?php

namespace Estrutura\Filter;

use RiskManager\OData\People;
use Zend\Filter\AbstractFilter;

class PeopleRM extends AbstractFilter
{
    public function filter($value)
    {
        if($value == '') return '';

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

        return $people;
    }
}
