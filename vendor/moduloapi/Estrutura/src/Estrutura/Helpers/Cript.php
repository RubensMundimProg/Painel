<?php
namespace Estrutura\Helpers;

/**
 *
 * @author ronaldo
 *
 */
class Cript {

    /**
     * @var int
     */
    public static $chave = 97;

    /**
     * @var string
     */
    public static $add_text = "projeto";

    /**
     * @param string Palavra
     * @return string
     */
    public static function enc($word) {
        $word .= md5(sha1(Cript::$add_text));
        $s = strlen($word) + 1;
        $nw = "";
        $n = Cript::$chave;
        for ($x = 1; $x < $s; $x++) {
            $m = $x * $n;
            if ($m > $s) {
                $nindex = $m % $s;
            } else if ($m < $s) {
                $nindex = $m;
            }
            if ($m % $s == 0) {
                $nindex = $x;
            }
            $nw = $nw . $word[$nindex - 1];
        }
        return $nw;
    }

    /**
     * @param string Palavra
     * @return string
     */
    public static function dec($word) {
        $s = strlen($word) + 1;
        $nw = "";
        $n = Cript::$chave;
        for ($y = 1; $y < $s; $y++) {
            $m = $y * $n;
            if ($m % $s == 1) {
                $n = $y;
                break;
            }
        }
        for ($x = 1; $x < $s; $x++) {
            $m = $x * $n;
            if ($m > $s) {
                $nindex = $m % $s;
            } else if ($m < $s) {
                $nindex = $m;
            }
            if ($m % $s == 0) {
                $nindex = $x;
            }
            $nw = $nw . $word[$nindex - 1];
        }
        $t = strlen($nw) - strlen(md5(sha1(Cript::$add_text)));
        return substr($nw, 0, $t);
    }
}
