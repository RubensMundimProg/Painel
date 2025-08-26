<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\Info as Entity;
use Base\Service\Request;

/**
 * 
 * Classe Service que retorna informações sobre a versão do 
 * Módulo Risk Manager que está sendo acessada.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Info extends Entity {

    protected $url = '/api/info';
    protected $moduloRequest = 'RM';

    /**
     * 
     */
    public function __construct()
    {

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $this->exchangeArray($request->send());
    }

}
