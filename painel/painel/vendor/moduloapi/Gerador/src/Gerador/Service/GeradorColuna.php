<?php

namespace Gerador\Service;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class GeradorColuna extends \Gerador\Entity\GeradorColuna{
    public function select($where=null){
        $select = new Select();
        $select->from($this->getTable()->table);
        $select->columns($this->getTable()->getColumns());
        $select->where($where);

        /** @var $lista GeradorColuna[] */
        /** @var $tratado GeradorColuna[] */
        $lista = $this->getTable()->select($select);
        $tratado = [];
        foreach($lista as $item){
            if(($item->getTableSchema() == $this->getTableSchema()) && ($item->getTableName() == $this->getTableName())){
                $tratado[] = $item;
            }
        }

       return $tratado;
    }

    public function getConfig(){
        if(!$this->config){
            $gerador      = require(BASE_PATCH.'/config/autoload/gerador.php');
            $this->setTableSchema( $gerador['database'] );
            $this->config = $gerador['db'];
        }

        return $this->config;
    }
}