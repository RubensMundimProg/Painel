<?php

namespace RiskManager\MySpace\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações básicas sobre o token atual do usuário.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Me extends AbstractApiService {

    /**
     *
     * @var string 
     */
    protected $id;
    /**
     *
     * @var string 
     */
    protected $name;
    /**
     *
     * @var string 
     */
    protected $login;
    /**
     *
     * @var string 
     */
    protected $email;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

}
