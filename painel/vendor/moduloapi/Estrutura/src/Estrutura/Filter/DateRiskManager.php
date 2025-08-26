<?php

namespace Estrutura\Filter;

use Zend\Filter\AbstractFilter;

class DateRiskManager extends AbstractFilter
{
    public function filter($value)
    {
        if(str_replace(['/','_'],'',$value) == '') return '';

        $format = (preg_match('@/@',$value)) ? 'd/m/Y' : 'Y-m-d';
        $format .= (strlen($value) == 10) ? '' : ' H:i:s';

        $dateRm = $this->dateToEpoc($value,$format);
        return $dateRm;
    }

    public function epocToDate($dataRM, $formato='d/m/Y'){
        if(strlen($dataRM) == 21){
            $data=substr(preg_replace('/\/Date\((.*)\)\//i','$1',$dataRM),0,10);
            $data = gmdate('d/m/Y H:i:s',$data);
        } else {
            $data=substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i','$1',$dataRM),0,10);
            $GMT3=substr(preg_replace('/\/Date\((.*)\)\//i','$1',$dataRM),15,-2);
            $GMT3  = -$GMT3;
            $data = gmdate('d/m/Y H:i:s',$data + 3600*($GMT3+date("I")));
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $data);
        if($objDate) return $dateTime;
        return $dateTime->format($formato);
    }

    public function dateToEpoc($dataRM, $format='d/m/Y'){
        $dateTime = \DateTime::createFromFormat($format, $dataRM);
        if(!$dataRM) return null;

        $date = $dateTime->getTimestamp();
        $dataRM='/Date('.(string) $date.'000'.'-0300)/';
        return $dataRM;
    }
}
