<?php

namespace RiskManager\Workflow\Service;

use Base\Service\Request;
use RiskManager\Workflow\Entity\Event as Entity;

/**
 *
 * Classe Service que processa os dados das queries
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Workflow\Service
 */
class Queries extends Entity{
    protected $url = '/api/queries';
    protected $moduloRequest = 'WF';

    protected $dataFilter = [];

    public function fetchAll(){
        if(!$this->getId()){
            throw new \Exception('Informe o ID da Consulta');
        }

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url.'/'.$this->getId());
        $request->setService($this);
        $dados = $request->send();

        $tratado = [];
        foreach($dados as $item){
            if(count($this->dataFilter)){
                foreach($this->dataFilter as $keyFilter => $filter){
                   if(isset($item->$keyFilter) && $item->$keyFilter == $filter){
                       $tratado[] = $item;
                   }
                }
            }else{
                $tratado[] = $item;
            }
        }
        return $tratado;
    }

    public function setDataFilter($array)
    {
        $this->dataFilter = $array;
    }

    public function count()
    {
        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url.'/'.$this->getId().'/count');
        $request->setService($this);
        $dados = $request->send();

        return $dados;
    }

}