<?php

namespace Estrutura\Table;


use Zend\Db\Sql\Select;

class AbstractEstruturaTable {
    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;
    public $table;
    public $campos;

    public function __construct(){}

    public function setTableGateway($tableGateway){
        if(!$this->tableGateway)
            $this->tableGateway = $tableGateway;
    }

    public function select($where=[]){
        return $this->tableGateway->select($where);
    }

    public function inserir($dados){
        $this->tableGateway->insert($dados);
        return $this->tableGateway->getLastInsertValue();
    }
    public function atualizar($dados, $where){
        $this->tableGateway->update($dados, $where);
    }

    public function salvar($dados, $where){
        if($where){
            $this->atualizar($dados, $where);
        }else{
            return $this->inserir($dados);
        }

        return true;
    }

    public function delete($where){
        $this->tableGateway->delete($where);
    }

    public function getColumns(){
        $colunas = [];
        foreach($this->campos as $key => $value){
            $colunas[$value] = $key;
        }
        return $colunas;
    }
} 