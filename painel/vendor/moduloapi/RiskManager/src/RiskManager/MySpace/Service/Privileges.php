<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\Privileges as Entity;
use Base\Service\Request;
/**
 * 
 * Classe Service que retorna os privilégios do token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Privileges extends Entity {

    protected $url = '/api/info/me/privileges';
    protected $moduloRequest = 'RM';
    protected $privileges = [];

    /**
     * 
     */
    public function __construct()
    {

        $request = new Request();
        $request->setType('GET');
        $request->setUrl($this->url);
        $request->setService($this);
        $dados = $request->send();

        if (!empty($dados)) {
            foreach ($dados as $privilege) {
                $privilegeService = new Privileges();
                $privilegeService->exchangeArray($privilege);
                $this->privileges[$privilegeService->getCode()] = $privilegeService;
            }
        }
    }

    /**
     * Retorna todos os previlégio do token atual do usuário
     * 
     * @return array
     */
    public function getPrivileges()
    {

        return $this->privileges;
    }

    /**
     * Retorna o previlégio do token atual do usuário, caso não exista o previlégio retorna FALSE
     * 
     * @param string $code Código do privilégio
     * @return mixed
     */
    public function getPrivilege($code)
    {

        if (!$code) {

            return FALSE;
        }

        if (isset($this->privileges[$code])) {

            return $this->privileges[$code];
        }

        return FALSE;
    }

}
