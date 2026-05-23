<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\Preferences as Entity;
use Base\Service\Request;
/**
 * 
 * Classe Service que retorna informações sobre as preferências pessoais do usuário atual.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Preferences extends Entity {

    protected $url = '/api/info/me/preferences';
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
