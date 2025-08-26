<?php

namespace RiskManager\Organization\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações dos ativos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Organization\Entity
 */
class Perimeter extends AbstractApiService {
    protected $id;
    protected $Name;
    protected $Path;
    protected $Description;
    protected $Longitude;
    protected $Latitude;
    protected $GeolocationDescription;
    protected $AdditionalInformation;
    protected $ResponsibleId;
    protected $ResponsibleName;
    protected $ResponsibleEmail;
    protected $ResponsiblePhone;

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
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @param mixed $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->Path;
    }

    /**
     * @param mixed $Path
     */
    public function setPath($Path)
    {
        $this->Path = $Path;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @param mixed $Description
     */
    public function setDescription($Description)
    {
        $this->Description = $Description;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->Longitude;
    }

    /**
     * @param mixed $Longitude
     */
    public function setLongitude($Longitude)
    {
        $this->Longitude = $Longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->Latitude;
    }

    /**
     * @param mixed $Latitude
     */
    public function setLatitude($Latitude)
    {
        $this->Latitude = $Latitude;
    }

    /**
     * @return mixed
     */
    public function getGeolocationDescription()
    {
        return $this->GeolocationDescription;
    }

    /**
     * @param mixed $GeolocationDescription
     */
    public function setGeolocationDescription($GeolocationDescription)
    {
        $this->GeolocationDescription = $GeolocationDescription;
    }

    /**
     * @return mixed
     */
    public function getAdditionalInformation()
    {
        return $this->AdditionalInformation;
    }

    /**
     * @param mixed $AdditionalInformation
     */
    public function setAdditionalInformation($AdditionalInformation)
    {
        $this->AdditionalInformation = $AdditionalInformation;
    }

    /**
     * @return mixed
     */
    public function getResponsibleId()
    {
        return $this->ResponsibleId;
    }

    /**
     * @param mixed $ResponsibleId
     */
    public function setResponsibleId($ResponsibleId)
    {
        $this->ResponsibleId = $ResponsibleId;
    }

    /**
     * @return mixed
     */
    public function getResponsibleName()
    {
        return $this->ResponsibleName;
    }

    /**
     * @param mixed $ResponsibleName
     */
    public function setResponsibleName($ResponsibleName)
    {
        $this->ResponsibleName = $ResponsibleName;
    }

    /**
     * @return mixed
     */
    public function getResponsibleEmail()
    {
        return $this->ResponsibleEmail;
    }

    /**
     * @param mixed $ResponsibleEmail
     */
    public function setResponsibleEmail($ResponsibleEmail)
    {
        $this->ResponsibleEmail = $ResponsibleEmail;
    }

    /**
     * @return mixed
     */
    public function getResponsiblePhone()
    {
        return $this->ResponsiblePhone;
    }

    /**
     * @param mixed $ResponsiblePhone
     */
    public function setResponsiblePhone($ResponsiblePhone)
    {
        $this->ResponsiblePhone = $ResponsiblePhone;
    }
}
