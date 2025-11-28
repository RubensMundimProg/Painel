<?php

namespace Modulo\Service;

class Curl {
    private $curl = null;

    public function __construct($url = null){
        return $this->init($url);
    }

    public function __call($n,$p){
        if($n=='init' || $n=='multi_init'){
            if($this->curl) curl_close($this->curl);
            return $this->curl = call_user_func_array('curl_'.$n,$p);
        } else {
            array_unshift($p,$this->curl);
            return call_user_func_array('curl_'.$n,$p);
        }
    }
} 