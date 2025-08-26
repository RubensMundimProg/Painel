<?php

namespace RiskManager\MySpace\Model;

/**
 * 
 * Classe Model que retorna informações sobre a versão do 
 * Módulo Risk Manager que está sendo acessada.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Info {

    public $campos = [
        'Version' => 'version',
        'RiskManagerUrl' => 'riskManagerUrl',
        'WorkflowServicesUrl' => 'workflowServicesUrl',
    ];

}
