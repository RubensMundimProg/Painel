<?php

namespace RiskManager\MySpace\Model;

/**
 * 
 * Classe Model que retorna uma lista com todas as consultas a que o usuário tem acesso.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class ListQueries {

    public $campos = [
        'Name' => 'name',
        'Description' => 'description',
        'Id' => 'id',
        'Type' => 'type',
        'UpdatedOn' => 'updatedOn',
        'Links' => 'links',
    ];

}
