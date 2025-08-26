<?php

namespace RiskManager\Workflow\Service;

use Base\Service\Request;
use RiskManager\Workflow\Entity\Attributes as Entity;

/**
 *
 * Classe Service que lista os detalhes de attributos customizados
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Workflow\Service
 */
class Attributes extends Entity{
    protected $url = '/api/info/attributes/';
    protected $moduloRequest = 'WF';

    public function __construct($objeto){
        $this->setAnonimo();

        $this->url .= $objeto;

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $data = $request->send();
        $this->exchangeArray($data);
        return $this;
    }
}