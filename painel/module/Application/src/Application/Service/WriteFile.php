<?php

namespace Application\Service;

class WriteFile
{
    protected $patch = './data/arquivos/sessao/';

    public function write($arquivo, $data){
        $json = json_encode($data);
        file_put_contents($this->patch.$arquivo.'.json',$json);
    }

    public function get($arquivo){
        $json = file_get_contents($this->patch.$arquivo.'.json');
        $data = json_decode($json, true);
        return $data;
    }
}