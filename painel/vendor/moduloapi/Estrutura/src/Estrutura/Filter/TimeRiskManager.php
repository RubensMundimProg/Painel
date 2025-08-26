<?php

namespace Estrutura\Filter;

use Zend\Filter\AbstractFilter;

class TimeRiskManager extends AbstractFilter
{
    public function filter($value)
    {

        $value = trim(substr($value, 0, 5));

        $value.=':00';

        $dateRm = $this->dateToEpoc($value,'H:i:s');
        return $dateRm;
    }

    public function epocToDate($dataRM, $formato='d/m/Y'){
        $data=substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i','$1',$dataRM),0,10);
        $GMT3=substr(preg_replace('/\/Date\((.*)\)\//i','$1',$dataRM),15,-2);
        $GMT3  = -$GMT3;
        $data = gmdate('d/m/Y H:i:s',$data + 3600*($GMT3+date("I")));
        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $data);
        return $dateTime->format($formato);
    }

    public function dateToEpoc($dataRM, $format='d/m/Y'){
        $dateTime = \DateTime::createFromFormat($format, $dataRM);

        if(!$dateTime)
            return null;

        if(!$dataRM) return null;


        $date = $dateTime->getTimestamp();
        $dataRM='/Date('.(string) $date.'000'.'-0300)/';
        return $dataRM;
    }
}
