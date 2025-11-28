<?php

namespace Estrutura\ApiInteractor;


use Modulo\Service\RiskManager;
use RiskManager\OData\CustomAttributes;

class AbstractApiEvent {

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
        $lista = $api->getEvents(['EventType'=>'SAA','Status'=>1]);
        $dados = json_decode($lista);
       // debug($dados);
        $tratado = [];
        $class = get_class($this);

        foreach($dados as $item){
            $obj = new $class;
            $obj->populate($item);
            $tratado[] = $obj;
        }

        return $tratado;
    }

    public function select($where=''){
        $api = new RiskManager();
        $lista = $api->getEvents($where);
        return json_decode($lista);
    }

    public function salvar(){
        $this->preSave();

        $dados = $this->hydrate();
        $riskManager = new RiskManager();

        if($this->getCode()){
            $riskManager->changeEvent($this->getCode(), $dados);
            $result = $this->getCode();
        }else{
            $result = $riskManager->createEvent($dados);
        }

        $valida = json_decode($result);
        if($valida && !is_string($valida)){
            throw new \Exception($valida->error_details[0]);
        }

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

    public function epocToDate($dataRM, $formato='d/m/Y'){
        $data=substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i','$1',$dataRM),0,10);
        $GMT3=substr(preg_replace('/\/Date\((.*)\)\//i','$1',$dataRM),15,-2);
        $GMT3  = -$GMT3;
        $data = gmdate('d/m/Y H:i:s',$data + 3600*($GMT3+date("I")));
        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $data);
        return $dateTime->format($formato);
    }

    public function dateToEpoc($dataRM){
        $data=substr($dataRM,0,10);
        $hora=substr($dataRM,11,19);
        $dataSplit=explode("/",$data);
        $horaSplit=explode(":",$hora);
        $d=$dataSplit[0];
        $m=$dataSplit[1];
        $a=$dataSplit[2];
        $h=$horaSplit[0];
        $i=$horaSplit[1];
        $s=$horaSplit[2];
        $date=$a.'/'.$m.'/'.$d.' '.$h.':'.$i.':'.$s;
        @$dataRM='/Date(1406226413893-0300)/';
        return $dataRM;//   \/DATA(12312313123)\/
    }


}