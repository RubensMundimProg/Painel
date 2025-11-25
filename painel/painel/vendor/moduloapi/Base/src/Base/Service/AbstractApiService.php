<?php

namespace Base\Service;

use Modulo\Service\OAuth;
use RiskManager\OData\CustomAttributes;
use RiskManager\OData\Filter;
use Zend\Session\Container;
use Zend\Stdlib\Hydrator\ArraySerializable;

/**
 * Classe Abstrata para os serviços da api
 *
 * @author Bruno Moraes <bruno.silva@modulo.com>
 * @version 1.0
 * @copyright  GPL © 2006, genilhu ltda.
 * @access public
 * @package RiskManager
 * @subpackage Base\Service
 */
class AbstractApiService {
     /**
     * Url base para execução das requisções
     * @access public
     * @name $url
     */
     protected $url;

    /**
     * Define se a classe dispara para a url do RiskManager de WF ou de RM
     * @access public
     * @name $moduloRequest
     */
     protected $moduloRequest;

    protected $filter;

    /**
     * Retorna a string base da service
     * @return string
     */
     public function getUrl(){
         return $this->url;
     }

    /**
     * Retorna o nome do modulo setado no Service
     * @return string
     */
     public function getModuloRequest(){
         return $this->moduloRequest;
     }


     /**
     * Popula o objeto baseado em um array
     * @access public
     * @param Array $data
     */
     public function exchangeArray($data) {
         foreach ($data as $chave => $item) {
            $metodo = 'set' . strtoupper($chave);
            if (method_exists($this, $metodo)) {
                $this->$metodo($item);
            }
        }
    }

    /**
     * Transforma o objeto atual em um array
     * @access public
     * @return Array $item;
     */
    public function toArray() {
        $classe = new \ReflectionClass($this);
        $item = [];

        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();

        foreach ($classe->getProperties() as $property) {
            if (!preg_match('/Entity/', $property->getDeclaringClass()->getName())) continue;
            $valor = method_exists($this, 'get' . $filter->filter($property->getName())) ? $this->{'get' . $filter->filter($property->getName())}() : null;
            $item[$property->getName()] = $valor;
        }

        return $item;
    }

    /**
     * Cria o enlace entre os dados setados e o nome na Model
     * @access public
     * @param Array $attribute
     * @param Bool clear
     * @return Array $arr
     */
    public function hydrate($attribute = null, $clear = true) {
        $this->setHydrator();
        $obj = $this;
        $arr = $this->hydrator->extract($obj);

        if ($clear) {
            $arr = array_filter($arr, function($item) {
                return $item !== null;
            });
        }

        if ($attribute) {
            if (is_string($attribute)) $attribute = array($attribute);
            $arrFields = array_intersect($this->table->campos, $attribute);
            $arrFields = array_keys($arrFields);
            $arrFiltrado = array();
            foreach ($arrFields as $field) {
                $arrFiltrado[$field] = array_key_exists($field, $arr) ? $arr[$field] : null;
            }
            $arr = $arrFiltrado;
        }

        if($this->customAttributes != false && count($this->customAttributes)){
            $arr['CustomAttributes'] = $this->customAttributes;
        } else {
            unset($arr['CustomAttributes']);
        }

        foreach($arr as $key => $item){
            if($item instanceof \RiskManager\OData\People){
                $arr[$key] = $item->getData();
            }
        }

        return($arr);
    }

    /**
     * Seta o hydrator para reflexão da classe
     * @access private
     */
    private function setHydrator(){
        $tableName = $this->getModelName();
        if (!class_exists($tableName))
            throw new \Exception('Model: '.$tableName.' Not Found');

        $this->table = new $tableName();
        $this->hydrator = (isset($this->table)) ? new \Estrutura\Table\TableEntityMapper($this->table->campos) : new ArraySerializable();
    }

    /**
     * Retorna o nome da model do objeto
     * @access private
     * @return String $classe
     */
    private function getModelName(){
        $classe = str_replace('\Service\\', '\Model\\', get_class($this));
        return $classe;
    }

    /**
     * Faz as verificações antes de salvar estourando Exception caso encontre falhas
     * @access public
     */
    public function preSave(){}

    /**
     * Faz as verificações depois de salvar estourando Exception caso encontre falhas
     * @access public
     */
    public function posSave(){}

    /**
     * Faz as verificações antes de excluid estourando Exception caso encontre falhas
     * @access public
     */
    public function preDelete(){}

    /**
     * Faz as verificações depois de salvar estourando Exception caso encontre falhas
     * @access public
     */
    public function posDelete(){}

    public function setFilter(Filter $filter){
        $this->filter = $filter;
    }

    /**
     * Retorna a instancia do filter
     * @return Filter
     */
    public function getFilter(){
        return $this->filter;
    }

    /**
     * Gerencia dos atributos customizados
     * @var
     */
    protected $customAttributes = false;

    /**
     * Seta os atributos customizados
     * @param CustomAttributes $custom
     */
    public function setCustomAttributes($custom){
        if($custom instanceof CustomAttributes)
            $this->customAttributes = $custom->getCampos();
        else if(is_object($custom))
            $this->customAttributes = (array) $custom;
        else if(is_array($custom))
            $this->customAttributes = $custom;
        else
            throw new \Exception('Tipo de Dados não tratado para o objeto customizado');
    }

    /**
     * Retorna os dados de objeto customizado do service
     * @return array|bool
     */
    public function getCustomAttributes($array=false,$key=false){

        /// Retorna o dado chaveado de acordo com a solicitação de KEY
        if($key) return (!isset($this->customAttributes[$key])) ? '' : $this->customAttributes[$key];
        if(!$this->customAttributes) return false;
        if(!$array) return $this->toObject($this->customAttributes);

        /// Retorna um array
        return $this->customAttributes;
    }

    public function getCustomAttribute($attr){
        $custom = $this->getCustomAttributes(true);
        if(isset($custom[$attr])) return $custom[$attr];

        return '';
    }

    public function toObject($array){
        $obj = new \StdClass();
        foreach($array as $key => $item){
            $obj->$key = $item;
        }

        return $obj;
    }

    protected $isAnonimo = false;

    public function isAnonimo(){
        return $this->isAnonimo;
    }

    public function setAnonimo(){
        $this->isAnonimo = true;
        $container = new Container('Anonimo');
        $dados = $this->authAnonymous();
        $container->offsetSet('dados',$dados);
    }

    /**
     * Autenticaçção Anonima para o console
     */
    public function authAnonymous() {

        if(!file_exists('./data/token.json')){
            $dados = $this->updateToken();
        }else{
            $dados = json_decode(file_get_contents('./data/token.json'), true);
            $save = \DateTime::createFromFormat('Y-m-d H:i:s', $dados['data_hora']);
            $now = new \DateTime();

            $diff = $save->diff($now);
            if($diff->d >= 1){
                $dados = $this->updateToken();
            }
        }

        return $dados;
    }

    public function updateToken(){
        $config = \Estrutura\Service\Config::getConfig('API');

        $host = str_replace(['https://','/'],'',$config['baseRM']);
        $url_rm = $config['baseRM'] . $config['patchRM'];
        $url_wf = $config['baseRM'] . $config['workFlowRM'];

        $params = [
            "client_id" => $config['idRM'],
            "client_secret" => $config['secretRM'],
            "grant_type" => "client_credentials"
        ];

        $head = "Host: " . $host;
        $head .= " Content-Type: application/x-www-form-urlencoded";

        // Cria sessão URL
        $ch = curl_init();

        // Prepara parâmetros cURL
        curl_setopt($ch, CURLOPT_URL, $url_rm . "/APIIntegration/Token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HEADER, $head);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CAINFO, 0);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $exec = curl_exec($ch);
        $dados = json_decode($exec);

        if(!$dados || preg_match('/erro/', $exec)){
            echo 'Erro ao requisitar token anonimo';
            die;
        }

        curl_close($ch);

        $dados = [
            'token'=>$dados->access_token,
            'host'=>$host,
            'urlRm'=>$url_rm,
            'urlWf'=>$url_wf,
            'data_hora'=>date('Y-m-d H:i:s')
        ];
        file_put_contents('./data/token.json', json_encode($dados));
        return $dados;
    }

    public function epocToDate($dataRM, $formato='d/m/Y'){
        $dataRM = str_replace(['-0100', '-0200', '-0400', '-0500'], '-0300', $dataRM);
        $onlyDate = false;
        if (!preg_match('/-0300/', $dataRM)) {
            $dataRM = str_replace(')/', '-0300)/', $dataRM);
            $onlyDate = true;
        }
        $data = substr(preg_replace('/\/Date\((.*)-[0-9]*\)\//i', '$1', $dataRM), 0, 10);
        $GMT3 = substr(preg_replace('/\/Date\((.*)\)\//i', '$1', $dataRM), 15, -2);
        $GMT3 = -$GMT3;
        $data = gmdate('d/m/Y H:i:s', $data + 3600 * ($GMT3 + date("I")));
        $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $data);
        if ($onlyDate) $dateTime->add(new \DateInterval('P1D'));
        return $dateTime->format($formato);
    }

    public function dateToEpoc($dataRM, $format='d/m/Y'){
        $dateTime = \DateTime::createFromFormat($format, $dataRM);
        if(!$dateTime) return null;
        $date = $dateTime->getTimestamp();
        $dataRM='/Date('.(string) $date.'000'.'-0300)/';
        return $dataRM;
    }

} 