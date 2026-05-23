<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\Me as Entity;
use Base\Service\Request;
/**
 * 
 * Classe Service que retorna informações básicas sobre o token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Me extends Entity {

    protected $url = '/api/info/me';
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
