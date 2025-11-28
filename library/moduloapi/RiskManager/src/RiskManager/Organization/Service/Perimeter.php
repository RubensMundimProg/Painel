<?php

namespace RiskManager\Organization\Service;

use Base\Service\Request;
use RiskManager\Organization\Entity\Perimeter as Entity;

/**
 *
 * Classe Service que processa informações dos perimetros
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Service
 */
class Perimeter extends Entity{
    protected $url = '/api/Organization/perimeters';
    protected $moduloRequest = 'RM';

    public function save(){
        if($this->getId()){
            return $this->update();
        }

        return $this->insert();
    }

    public function insert()
    {

        $request = new Request();
        $request->setType('POST');
        $request->setUrl($this->url);
        $request->setService($this);
        $id = $request->send();
        $this->setId($id);
        return $this;
    }

    public function update(){
        if(!$this->getId()){
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('PUT');
        $request->setUrl($this->url.'/'.$this->getId());
        $request->setService($this);
        $request->send();

        return $this;
    }

    public function fetchAll(){
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $dados = $request->send();
        $lista = [];
        foreach($dados as $item){
            $this->exchangeArray($item);
            $lista[] = clone($this);
        }

        return $lista;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function load(){
        if(!$this->getId()){
            throw new \Exception('Favor informar o ID');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url.'/'.$this->getId());
        $request->setService($this);
        $result = $request->send();
        $this->exchangeArray($result);
        return $this;
    }
}