<?php

namespace Estrutura\ApiInteractor;


use Modulo\Service\RiskManager;

class AbstractApiInteractor {

    protected $hydrator;
    protected $model;

    public function populate($dados){
        $classe = str_replace('Service','Model', get_class($this));
        $model = new $classe;
        $dados = (array) $dados;
        $tratado = [];
        foreach($model->campos as $chave => $item){
            if(isset($dados[$chave]) && $dados[$chave] ==! null){
                if($dados){
                    if(in_array($chave, $dados))
                        $tratado[$item] = $dados[$chave];
                }
            }
        }

        $this->exchangeArray($tratado);
    }

    public function exchangeArray($data){
        foreach($data as $chave => $item){
            $metodo = 'set'.strtoupper($chave);
            if(method_exists($this, $metodo)){
                $this->$metodo($item);
            }
        }
    }

    public function toArray(){
        $classe = new \ReflectionClass($this);
        $item = [];

        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();

        foreach( $classe->getProperties() as $property){
            if(!preg_match('/Entity/',$property->getDeclaringClass()->getName())) continue;
            $valor = method_exists( $this , 'get'.$filter->filter($property->getName()) ) ? $this->{'get'.$filter->filter($property->getName())}() : null ;

            if($valor instanceof AbstractApiInteractor )
                $item[$property->getName()] = $valor->toArray();
            elseif ($valor instanceof \DateTime )
                $item[$property->getName()] = $valor->format('d/m/Y');
            else
                $item[$property->getName()] = $valor;
        }

        return $item;
    }

    public function hydrate($attribute=null,$clear=true){
        $classe = str_replace('Service','Model', get_class($this));
        $model = new $classe;
        $dados = $this->toArray();

        $tratado = [];
        foreach($model->campos as $chave => $item){
            if(isset($dados[$item]) && $dados[$item] ==! null){
                if($attribute){
                    if(in_array($item, $attribute))
                        $tratado[$chave] = $dados[$item];
                }else{
                    $tratado[$chave] = $dados[$item];
                }

            }
        }

       return($tratado);
    }

    public function fetchAll(){
        $api = new RiskManager();
        $lista = $api->getEvents();
        return json_decode($lista);
    }

    public function select($where=''){
        $api = new RiskManager();
        $lista = $api->getEvents($where);
        return json_decode($lista);
    }

    public function filtrarObjeto(){
        debug(1);
        $where = $this->hydrate();
        $api = new RiskManager();
        $lista = $api->getEvents($where);
        return json_decode($lista);
    }

    public function salvar(){
        $this->preSave();

        $dados = $this->hydrate();
        $riskManager = new RiskManager();
        $result = $riskManager->createEvent($dados);

        $this->posSave();
        return $result;
    }

    public function preSave(){}
    public function posSave(){}

    public function excluir(){
        $arr = $this->hydrate();
        $this->getTable()->delete($arr);
    }

    public function buscar($code)
    {
        $riskManager = new RiskManager();
        $dados = $riskManager->getEventByCode($code);
        $dados = json_decode($dados);
        $this->populate($dados);
        return $this;
    }

    public function fieldName($attribute)
    {
        return (($chave = array_search($attribute,$this->table->campos))!==false) ? $chave : null ;
    }
}