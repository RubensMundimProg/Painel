<?php

namespace RiskManager\ERM\Model;

/**
 *
 * Classe Model que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Loss
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage ERM\Model
 */
class Loss {

    public $campos = [
        'Id' => 'id',
        'Name' => 'name',
        'Description' => 'description',
        'Deleted' => 'deleted',
        
        'LossEventResponsible' => 'lossEventResponsible',
        'Value' => 'value',
        'DateCreated' => 'dateCreated',
        'DateUpdated' => 'dateUpdated',
        'AccountingDate' => 'accountingDate',
        'Type' => 'type',
    ];
}
