<?php

namespace RiskManager\Organization\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações dos grupos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Organization\Entity
 */
class Group extends AbstractApiService {
    protected $id;
    protected $name;
    protected $description;
    protected $email;
    protected $additionalInformation;
    protected $responsibleId;
    protected $responsibleName;
    protected $responsibleEmail;
    protected $responsiblePhone;
    protected $responsible;

    /**
     * @return mixed
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * @param mixed $responsible
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;
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
    public function getResponsibleEmail()
    {
        return $this->responsibleEmail;
    }

    /**
     * @param mixed $responsibleEmail
     */
    public function setResponsibleEmail($responsibleEmail)
    {
        $this->responsibleEmail = $responsibleEmail;
    }

    /**
     * @return mixed
     */
    public function getResponsibleId()
    {
        return $this->responsibleId;
    }

    /**
     * @param mixed $responsibleId
     */
    public function setResponsibleId($responsibleId)
    {
        $this->responsibleId = $responsibleId;
    }

    /**
     * @return mixed
     */
    public function getResponsibleName()
    {
        return $this->responsibleName;
    }

    /**
     * @param mixed $responsibleName
     */
    public function setResponsibleName($responsibleName)
    {
        $this->responsibleName = $responsibleName;
    }

    /**
     * @return mixed
     */
    public function getResponsiblePhone()
    {
        return $this->responsiblePhone;
    }

    /**
     * @param mixed $responsiblePhone
     */
    public function setResponsiblePhone($responsiblePhone)
    {
        $this->responsiblePhone = $responsiblePhone;
    }
}