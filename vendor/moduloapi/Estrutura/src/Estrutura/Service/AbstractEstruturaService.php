<?php

namespace Estrutura\Service;

use Estrutura\Table\AbstractEstruturaTable;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ArraySerializable;

class AbstractEstruturaService {

    /**
     * @var AbstractEstruturaTable
     */
    protected $table;
    protected $config;
    protected $hydrator;
    protected static $adapter;
    protected static $sm;

    public static function setServiceManager($sm) {
        self::$sm = $sm;
    }

    public function getServiceLocator() {
        return self::$sm;
    }

    public function exchangeArray($data) {
        foreach ($data as $chave => $item) {
            if(preg_match('/_/',$chave)){
                $metodo = 'set'.str_replace(' ','',ucfirst(str_replace('_',' ',$chave)));
            }else{
                $metodo = 'set' . strtoupper($chave);
            }
            if (method_exists($this, $metodo)) {
                $this->$metodo($item);
            }
        }
    }

    public function toDate($field, $format='Y-m-d'){
        $method = 'get'.$field;
        $dateTime = \DateTime::createFromFormat($format, $this->{$method}());
        return $dateTime;
    }

    public function getTable() {
        if (!$this->table) {
            $dbAdapter = $this->getAdapter();

            $tableName = $this->getTableName();
            if (class_exists($tableName)) {
                $this->table = new $tableName();
            }

            $this->hydrator = (isset($this->table) && isset($this->table->table) ) ? new \Estrutura\Table\TableEntityMapper($this->table->campos) : new ArraySerializable();
            $rowObjectPrototype = $this;

            $resultSet = new \Zend\Db\ResultSet\HydratingResultSet($this->hydrator, $rowObjectPrototype);
            $tableGateway = new TableGateway(isset($this->table->table) ? $this->table->table : '', $dbAdapter, null, $resultSet);
            $this->table->setTableGateway($tableGateway);
        }
        return $this->table;
    }

    public function toArray() {
        $classe = new \ReflectionClass($this);
        $item = [];

        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();

        foreach ($classe->getProperties() as $property) {
            if (!preg_match('/Entity/', $property->getDeclaringClass()->getName()))
                continue;
            $valor = method_exists($this, 'get' . $filter->filter($property->getName())) ? $this->{'get' . $filter->filter($property->getName())}() : null;

            if ($valor instanceof AbstractEstruturaService)
                $item[$property->getName()] = $valor->toArray();
            elseif ($valor instanceof \DateTime)
                $item[$property->getName()] = $valor->format('d/m/Y');
            else
                $item[$property->getName()] = $valor;
        }

        return $item;
    }

    public function hydrate($attribute = null, $clear = true) {
        $this->getTable();
        $obj = $this;
        $arr = $this->hydrator->extract($obj);

        if ($clear) {
            $arr = array_filter($arr, function($item) {
                return $item !== null;
            });
        }

        if ($attribute) {
            if (is_string($attribute))
                $attribute = array($attribute);
            $arrFields = array_intersect($this->table->campos, $attribute);
            $arrFields = array_keys($arrFields);
            $arrFiltrado = array();
            foreach ($arrFields as $field) {
                $arrFiltrado[$field] = array_key_exists($field, $arr) ? $arr[$field] : null;
            }
            $arr = $arrFiltrado;
        }

        return($arr);
    }

    private function getTableName() {
        $obj = str_replace('\Service\\', '\Table\\', get_class($this));
        return $obj;
    }

//    public function setResultset($dados){
//        $this->exchangeArray($dados);
//    }

    public function getConfig() {
        if (!$this->config) {
            $this->config = Config::getConfig('db');
        }
        return $this->config;
    }

    public function getAdapter() {

        if (!self::$adapter) {            
            
            self::$adapter = new \Zend\Db\Adapter\Adapter($this->getConfig());
        }
        return self::$adapter;
    }

    public function fetchAll() {
        return $this->select();
    }

    public function select($where = null) {
        return $this->getTable()->select($where);
    }

    public function filtrarObjeto() {
        $where = $this->hydrate();
        $wTratado = new Where();
        foreach ($where as $chave => $valor) {
            if ($chave == 'NOME') {
                $wTratado->like($chave, '%' . $valor . '%');
            } else {
                $wTratado->equalTo($chave, $valor);
            }
        }
        return $this->select($wTratado);
    }

    public function salvar() {
        $this->preSave();

        $dados = $this->hydrate();

        $where = null;

        if ($this->getId()) {
            if (!$field = $this->fieldName('id')) {
                $field = $this->fieldName('Id');
            }

            $where = [$field => $this->getId()];
        }

        $result = $this->getTable()->salvar($dados, $where);
        if (is_string($result)) {
            $this->setId($result);
        }
        $this->posSave();
        return $this;
    }

    public function preSave() {
        
    }

    public function posSave() {
        
    }

    public function excluir() {
        $arr = $this->hydrate();
        $this->getTable()->delete($arr);
    }

    public function load() {
        $arr = $this->hydrate(['id']);

        $dados = $this->select($arr)->current();

        if ($dados) {
            $this->exchangeArray($dados->toArray());
            return $this;
        }

        return null;
    }

    public function buscar($id) {
        $this->setId($id);
        return $this->filtrarObjeto()->current();
    }

    public function fieldName($attribute) {
        return (($chave = array_search($attribute, $this->table->campos)) !== false) ? $chave : null;
    }

}
