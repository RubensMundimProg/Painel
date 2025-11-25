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
class Asset extends AbstractApiService {
    protected $id;
    protected $assetType;
    protected $name;
    protected $path;
    protected $responsible;
    protected $relevance;
    protected $criticality;
    protected $analysisFrequency;
    protected $description;
    protected $latitude;
    protected $longitude;
    protected $geolocationDescription;
    protected $zoomLevel;
    protected $customAttributes;
    protected $hostAddress;
    protected $credentials;
    protected $collectorServer;
    protected $netbiosName;
    protected $ipAddress;
    protected $dnsName;
    protected $ParentPerimeter;

    /**
     * @return mixed
     */
    public function getAnalysisFrequency()
    {
        return $this->analysisFrequency;
    }

    /**
     * @param mixed $analysisFrequency
     */
    public function setAnalysisFrequency($analysisFrequency)
    {
        $this->analysisFrequency = $analysisFrequency;
    }

    /**
     * @return mixed
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    /**
     * @param mixed $assetType
     */
    public function setAssetType($assetType)
    {
        $this->assetType = $assetType;
    }

    /**
     * @return mixed
     */
    public function getCollectorServer()
    {
        return $this->collectorServer;
    }

    /**
     * @param mixed $collectorServer
     */
    public function setCollectorServer($collectorServer)
    {
        $this->collectorServer = $collectorServer;
    }

    /**
     * @return mixed
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param mixed $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return mixed
     */
    public function getCriticality()
    {
        return $this->criticality;
    }

    /**
     * @param mixed $criticality
     */
    public function setCriticality($criticality)
    {
        $this->criticality = $criticality;
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
    public function getDnsName()
    {
        return $this->dnsName;
    }

    /**
     * @param mixed $dnsName
     */
    public function setDnsName($dnsName)
    {
        $this->dnsName = $dnsName;
    }

    /**
     * @return mixed
     */
    public function getGeolocationDescription()
    {
        return $this->geolocationDescription;
    }

    /**
     * @param mixed $geolocationDescription
     */
    public function setGeolocationDescription($geolocationDescription)
    {
        $this->geolocationDescription = $geolocationDescription;
    }

    /**
     * @return mixed
     */
    public function getHostAddress()
    {
        return $this->hostAddress;
    }

    /**
     * @param mixed $hostAddress
     */
    public function setHostAddress($hostAddress)
    {
        $this->hostAddress = $hostAddress;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
    public function getNetbiosName()
    {
        return $this->netbiosName;
    }

    /**
     * @param mixed $netbiosName
     */
    public function setNetbiosName($netbiosName)
    {
        $this->netbiosName = $netbiosName;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * @param mixed $relevance
     */
    public function setRelevance($relevance)
    {
        $this->relevance = $relevance;
    }

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
    public function getZoomLevel()
    {
        return $this->zoomLevel;
    }

    /**
     * @param mixed $zoomLevel
     */
    public function setZoomLevel($zoomLevel)
    {
        $this->zoomLevel = $zoomLevel;
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
        str_replace('"','',$id);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getParentPerimeter()
    {
        return $this->ParentPerimeter;
    }

    /**
     * @param mixed $ParentPerimeter
     */
    public function setParentPerimeter($ParentPerimeter)
    {
        $this->ParentPerimeter = $ParentPerimeter;
    }
}
