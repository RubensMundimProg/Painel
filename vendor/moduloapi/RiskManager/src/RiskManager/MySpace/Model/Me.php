<?php

namespace RiskManager\MySpace\Model;

/**
 * 
 * Classe Model que retorna informações básicas sobre o token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Me {

    public $campos = [
        'Id' => 'id',
        'Name' => 'name',
        'Login' => 'login',
        'Email' => 'email',
    ];
}
