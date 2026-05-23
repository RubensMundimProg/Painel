<?php

namespace RiskManager\Organization\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações das pessoas
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Organization\Entity
 */
class People extends AbstractApiService {
    protected $id;
    protected $name;
    protected $description;
    protected $email;
    protected $phone;
    protected $additionalInformation;
    protected $login;
    protected $status;
    protected $hydrateArray = false;

    /**
     * @return boolean
     */
    public function getHydrateArray()
    {
        return $this->hydrateArray;
    }

    /**
     * @param boolean $hydrateArray
     */
    public function setHydrateArray($hydrateArray)
    {
        $this->hydrateArray = $hydrateArray;
    }



    /**
     * @return mixed
     */
    public function getAdditionalInformation()
    {
        return $this->additionalInformation;
    }

    /**
     * @param mixed $additionalInformation
     */
    public function setAdditionalInformation($additionalInformation)
    {
        $this->additionalInformation = $additionalInformation;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $id = str_replace('"','',$id);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    public function hydrate($attribute = null, $clear = true){

        $hydrate = parent::hydrate($attribute, $clear);

        if($this->getHydrateArray()){
            $hydrate = [$hydrate];
        }

        return $hydrate;

    }


}