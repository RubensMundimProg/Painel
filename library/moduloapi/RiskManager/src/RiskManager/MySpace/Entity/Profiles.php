<?php

namespace RiskManager\MySpace\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena os perfis de acesso do token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Profiles extends AbstractApiService {

    /**
     *
     * @var string 
     */
    protected $code;
    
    /**
     *
     * @var string 
     */
    protected $name;

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

}
