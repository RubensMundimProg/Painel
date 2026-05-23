<?php

namespace RiskManager\MySpace\Model;

/**
 * 
 * Classe Model que retorna informações sobre as preferências pessoais do usuário atual.
  .
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Preferences {

    public $campos = [
        'ItemsPerPage' => 'itemsPerPage',
        'Language' => 'language',
        'ShowGraphs' => 'showGraphs',
    ];

}
