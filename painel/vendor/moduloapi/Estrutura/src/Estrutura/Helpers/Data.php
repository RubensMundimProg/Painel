<?php

namespace Estrutura\Helpers;

/**
 *
 * @author ronaldo
 *
 */
class Data {

    public static $mes = array(1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro');

    /**
     *
     * @param string $date
     */
    public static function isDate($date) {

        $char = (strpos($date, '/') !== false) ? '/' : '-';
        $date_array = explode($char, $date);

        if (count($date_array) != 3) {

            return false;
        }

        return true;
    }

    public static function get($date, $tipo) {

        if ($date) {

            $zendDate = new Zend_Date($date);
            return $zendDate->get($tipo);
        } else {

            return null;
        }
    }

    /**
     *
     * @param Zend_Date $data
     * @return boolean
     */
    public static function isValid(Zend_Date $data) {

        $d = $data->get(Zend_Date::DAY);
        $m = $data->get(Zend_Date::MONTH);
        $y = $data->get(Zend_Date::YEAR);

        // verifica se a data é válida!
        $res = checkdate($m, $d, $y);
        if (($res == 1) && ($y > 1900)) {

            return true;
        } else {

            return false;
        }
    }

    /**
     *
     * @param Zend_Date $data
     * @return int $dias_diferenca
     */
    public static function calculaData(Zend_Date $data) {

        //defino data 1
        $ano1 = $data->get(Zend_Date::YEAR);
        $mes1 = $data->get(Zend_Date::MONTH);
        $dia1 = $data->get(Zend_Date::DAY);

        //defino data 2
        $ano2 = date('Y');
        $mes2 = date('m');
        $dia2 = date('d');

        //calculo timestam das duas datas
        $timestamp1 = mktime(0, 0, 0, $mes1, $dia1, $ano1);
        $timestamp2 = mktime(4, 12, 0, $mes2, $dia2, $ano2);

        //diminuo a uma data a outra
        $segundos_diferenca = $timestamp1 - $timestamp2;
        //echo $segundos_diferenca;
        //converto segundos em dias
        $dias_diferenca = $segundos_diferenca / (60 * 60 * 24);

        //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
        $dias_diferenca = abs($dias_diferenca);

        //tiro os decimais aos dias de diferenca
        $dias_diferenca = floor($dias_diferenca);

        return $dias_diferenca;
    }

    /**
     *
     * @param Zend_Date $data
     * @param string $formato => Podem ser 	1 => 25 de abril de 2012 às 10:50:58
     * 										2 => 25 de abril de 2012 às 10:50
     * 										3 => 25 de abril de 2012
     * @return string
     */
    public static function porExtenso(Zend_Date $data, $formato = 1) {

        $day = $data->get(Zend_Date::DAY);
        $month = $data->get(Zend_Date::MONTH);
        $year = $data->get(Zend_Date::YEAR);
        $hour = $data->get(Zend_Date::HOUR);
        $minute = $data->get(Zend_Date::MINUTE);
        $second = $data->get(Zend_Date::SECOND);

        $day = intval($day);
        $month = intval($month);

        $nomeMes = self::$mes;

        switch ($formato) {

            default:
                return $data;
                break;
            case 1:
                $mes = $nomeMes[$month];
                return $day . " de $mes de $year às $hour:$minute:$second";
                break;
            case 2:
                $mes = $nomeMes[$month];
                return "$day de $mes de $year às $hour:$minute";
                break;
            case 3:
                $mes = $nomeMes[$month];
                return "$day de $mes de $year";
                break;
        }
    }

    /*     * *************************************************************************
     * Function: converteData2RM
     * Description: Converte data do formato dd/mm/YYYY para formato RM (timestamp)  \/DATA(12312313123)\/
     * Parameters: $dataRM (string) - dd/mm/YYYY HH:i:s
     *           
     * ************************************************************************ */

    public static function converteData2RM($dataRM) {
        //$dataRM='03/11/2011 15:46:23';
        $data = substr($dataRM, 0, 10);
        $hora = substr($dataRM, 11, 19);
        $dataSplit = explode("/", $data);
        $horaSplit = explode(":", $hora);
        $d = $dataSplit[0];
        $m = $dataSplit[1];
        $a = $dataSplit[2];
                $h = $horaSplit[0];
        $i = $horaSplit[1];
        $s = $horaSplit[2];
        $date = $a . '/' . $m . '/' . $d . ' ' . $h . ':' . $i . ':' . $s;
        //echo $date." - ";
        //$dataRM=gmmktime($h,$i,$s,$m,$d,$a)."486";
        @$dataRM = '\/Date(' . gmmktime($h, $i, $s, $m, $d, $a) . "486" . ')\/';
        //echo $dataRM;
        //$params.= ', "data_inicio_planejado_qsocial": "\/Date('.gmmktime(0,0,0,substr($d,3,2),substr($d,0,2),substr($d,6,4))."486".')\/"';
        //$dataRM=strtotime($date);
        //$dataRM=$dataRM*100;
        //	echo $dataRM."<br />";
        
        return $dataRM; //   \/DATA(12312313123)\/
        //return $dataRM;
    }

    /*     * *************************************************************************
     * Function: converteData
     * Description: Converte data do formato do RM (timestamp) para dd/mm/YYYY
     * Parameters: $dataRM (string) - \/DATA(12312313123)\/
     *            $formato (int) - parametros aceitaveis 0,1,2,3
     * 
     * 3 Somente campo DATA dentro do RM
     * ************************************************************************ */

    public static function converteData($dataRM, $formato = NULL, $tipo = NULL) {
        if ($tipo == 'simples') {
            $data = substr($dataRM, 7, 13);
        } else {
            $data = substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i', '$1', $dataRM), 0, 10);
            $GMT3 = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 15, -2);
            $GMT3 = -$GMT3;
        }

        //echo $data;
        switch ($formato) {
            case 1://23/12/2012|23:01:50 para dar split com o retorno
                return gmdate('d/m/Y|H:i:s', $data + 3600 * ($GMT3 + date('I')));
                break;

            case 2: //23/12/2012 23:01:50
                return gmdate('d/m/Y H:i:s', $data + 3600 * ($GMT3 + date('I')));
                break;

            case 3://SOMENTE CAMPO DATA DENTRO DO RM
                $data = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 0, 10);
                return @gmdate('d/m/Y', $data);

            case 4://Retorna padrao para comparaçao de datas
                $data = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 0, 10);
                return gmdate('Y-m-d H:i:s', $data + 3600 * ($GMT3 + date('I')));

            case 5://Retorna padrao para comparaçao de datas
                $data = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 0, 10);
                return gmdate('Y-m-d', $data);

            default: //23/12/2012 23:01:50
                return gmdate('d/m/Y H:i:s', $data + 3600 * ($GMT3 + date('I')));
                break;
        }
    }

    /*     * ************************************************************************
     * Function: converteDataENPT
     * Description: Converte data do formato YYYY/mm/dd HH:MM:SS para formato dd/mm/YYYY HH:MM:SS
     * Parameters: $data (string) - YYYY/mm/dd HH:MM:SS
     * Author: Roberto Ritter
     * ************************************************************************ */

    public static function converteDataENPT($dataRM) {
        //$data='2011/11/31 15:46:23';
        $data = substr($dataRM, 0, 10);
        $hora = substr($dataRM, 11, 19);
        $dataSplit = explode('-', $data);
        $horaSplit = explode(':', $hora);
        $d = $dataSplit[2];
        $m = $dataSplit[1];
        $a = $dataSplit[0];
        $h = $horaSplit[0];
        $i = $horaSplit[1];
        $s = $horaSplit[2];
        $date = $a . '/' . $m . '/' . $d . ' ' . $h . ':' . $i . ':' . $s;
        //echo $date.' - ';
        //$dataRM=gmmktime($h,$i,$s,$m,$d,$a).'486';
        //@$dataRM='\/Date('.gmmktime($h,$i,$s,$m,$d,$a).'486'.')\/';
        $dataRM = $d . '/' . $m . '/' . $a . ' ' . $h . ':' . $i . ':' . $s;
        //echo $dataRM;
        //$params.= ', 'data_inicio_planejado_qsocial': '\/Date('.gmmktime(0,0,0,substr($d,3,2),substr($d,0,2),substr($d,6,4)).'486'.')\/'';
        //$dataRM=strtotime($date);
        //$dataRM=$dataRM*100;
        //	echo $dataRM.'<br />';
        return $dataRM; //   \/DATA(12312313123)\/
        //return $dataRM;
    }

}
