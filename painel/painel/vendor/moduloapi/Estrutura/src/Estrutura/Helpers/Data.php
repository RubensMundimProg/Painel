<?php

namespace Estrutura\Helpers;

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
     * Verifica se é uma data válida no formato brasileiro ou internacional
     */
    public static function isDate($date) {
        if (empty($date)) return false;
        
        // Remove qualquer caracter de tempo se houver
        $dateOnly = explode(' ', $date)[0];
        
        $char = (strpos($dateOnly, '/') !== false) ? '/' : '-';
        $date_array = explode($char, $dateOnly);

        if (count($date_array) != 3) {
            return false;
        }
        
        // Verifica formato dia/mes/ano ou ano-mes-dia
        if ($char == '/') {
            return checkdate($date_array[1], $date_array[0], $date_array[2]);
        } else {
            return checkdate($date_array[1], $date_array[2], $date_array[0]);
        }
    }

    /**
     * Recupera parte de uma data
     */
    public static function get($date, $tipo) {
        if ($date) {
            try {
                // Tenta converter formato brasileiro para DateTime
                if (strpos($date, '/') !== false) {
                    $date = str_replace('/', '-', $date);
                }
                
                $dt = new \DateTime($date);
                
                // Mapeamento de constantes antigas do Zend_Date
                switch ($tipo) {
                    case 'dd': 
                    case 'd':
                        return $dt->format('d');
                    case 'MM': 
                    case 'M':
                        return $dt->format('m');
                    case 'yyyy': 
                    case 'y':
                        return $dt->format('Y');
                    case 'HH': 
                    case 'H':
                        return $dt->format('H');
                    case 'mm': 
                    case 'm':
                        return $dt->format('i');
                    case 'ss': 
                    case 's':
                        return $dt->format('s');
                    default: 
                        return $dt->format($tipo);
                }
            } catch (\Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Verifica se o objeto ou string é uma data válida
     */
    public static function isValid($data) {
        if (empty($data)) return false;
        
        if ($data instanceof \DateTime) {
            return true;
        }
        
        try {
            new \DateTime($data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Calcula diferença em dias entre a data e hoje
     */
    public static function calculaData($data) {
        if (!($data instanceof \DateTime)) {
             try {
                // Corrige barras para hifens para o construtor
                $dataStr = str_replace('/', '-', $data);
                $dataObj = new \DateTime($dataStr);
             } catch (\Exception $e) {
                 return 0;
             }
        } else {
            $dataObj = $data;
        }

        $hoje = new \DateTime();
        $hoje->setTime(0, 0, 0);
        $dataObj->setTime(0, 0, 0);
        
        $diff = $hoje->diff($dataObj);
        
        return $diff->days;
    }

    /**
     * Formata data por extenso
     */
    public static function porExtenso($data, $formato = 1) {
        if (!($data instanceof \DateTime)) {
             try {
                $dataStr = str_replace('/', '-', $data);
                $data = new \DateTime($dataStr);
             } catch(\Exception $e) {
                 return $data;
             }
        }

        $day = $data->format('d');
        $month = (int)$data->format('m');
        $year = $data->format('Y');
        $hour = $data->format('H');
        $minute = $data->format('i');
        $second = $data->format('s');

        $nomeMes = self::$mes;

        switch ($formato) {
            default:
                return $data->format('d/m/Y H:i:s');
            case 1:
                $mes = $nomeMes[$month];
                return $day . " de $mes de $year às $hour:$minute:$second";
            case 2:
                $mes = $nomeMes[$month];
                return "$day de $mes de $year às $hour:$minute";
            case 3:
                $mes = $nomeMes[$month];
                return "$day de $mes de $year";
        }
    }

    public static function converteData2RM($dataRM) {
        $data = substr($dataRM, 0, 10);
        $hora = substr($dataRM, 11, 19);
        $dataSplit = explode("/", $data);
        $horaSplit = explode(":", $hora);
        
        if(count($dataSplit) < 3) return $dataRM;
        
        $d = $dataSplit[0];
        $m = $dataSplit[1];
        $a = $dataSplit[2];
        $h = isset($horaSplit[0]) ? $horaSplit[0] : '00';
        $i = isset($horaSplit[1]) ? $horaSplit[1] : '00';
        $s = isset($horaSplit[2]) ? $horaSplit[2] : '00';
        
        return '\/Date(' . gmmktime((int)$h, (int)$i, (int)$s, (int)$m, (int)$d, (int)$a) . "486" . ')\/';
    }

    public static function converteData($dataRM, $formato = NULL, $tipo = NULL) {
        if ($tipo == 'simples') {
            $data = substr($dataRM, 7, 13);
            $GMT3 = 0; // Assumindo 0 se for simples, ou ajuste conforme necessidade
        } else {
            $data = substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i', '$1', $dataRM), 0, 10);
            $GMT3 = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 15, -2);
            $GMT3 = (int)$GMT3 * -1; 
        }

        $timestamp = (int)$data + 3600 * ($GMT3 + (date('I') ? 1 : 0));

        switch ($formato) {
            case 1: return gmdate('d/m/Y|H:i:s', $timestamp);
            case 2: return gmdate('d/m/Y H:i:s', $timestamp);
            case 3: return gmdate('d/m/Y', $timestamp);
            case 4: return gmdate('Y-m-d H:i:s', $timestamp);
            case 5: return gmdate('Y-m-d', $timestamp);
            default: return gmdate('d/m/Y H:i:s', $timestamp);
        }
    }

    public static function converteDataENPT($dataRM) {
        $data = substr($dataRM, 0, 10);
        $hora = substr($dataRM, 11, 19);
        $dataSplit = explode('-', $data);
        $horaSplit = explode(':', $hora);
        
        if(count($dataSplit) < 3) return $dataRM;

        $d = $dataSplit[2];
        $m = $dataSplit[1];
        $a = $dataSplit[0];
        $h = isset($horaSplit[0]) ? $horaSplit[0] : '00';
        $i = isset($horaSplit[1]) ? $horaSplit[1] : '00';
        $s = isset($horaSplit[2]) ? $horaSplit[2] : '00';
        
        return $d . '/' . $m . '/' . $a . ' ' . $h . ':' . $i . ':' . $s;
    }
}