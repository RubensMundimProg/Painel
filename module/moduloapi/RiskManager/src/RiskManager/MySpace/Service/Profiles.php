<?php

namespace RiskManager\MySpace\Service;

use RiskManager\MySpace\Entity\Profiles as Entity;
use Base\Service\Request;
/**
 * 
 * Classe Service que retorna os perfis de acesso do token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Profiles extends Entity {

    protected $url = '/api/info/me/profiles';
    protected $moduloRequest = 'RM';
    protected $profiles = [];

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
            foreach ($dados as $profile) {
                $profilesService = new Profiles();
                $profilesService->exchangeArray($profile);
                $this->profiles[$profilesService->getCode()] = $profilesService;
            }
        }
    }

    /**
     * Retorna todos os perfis de acesso do token atual do usuário
     * 
     * @return array
     */
    public function getProfiles()
    {

        return $this->privileges;
    }

    /**
     * Retorna o perfil de acesso do token atual do usuário, caso não exista o previlégio retorna FALSE
     * 
     * @param string $code Código do perfil
     * @return mixed
     */
    public function getProfile($code)
    {

        if (!$code) {

            throw new \Exception('Favor informar o code');
        }

        if (!isset($this->profiles[$code])) {

            return FALSE;
        }

        return $this->profiles[$code];
    }

}
