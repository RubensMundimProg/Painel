<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class UltimaMilha extends AbstractEstruturaService{

    protected $Id;
    protected $Uf;
    protected $NomeEscola;
    protected $Status;
    protected $DataHora;
    protected $Latitude;
    protected $Longitude;
    protected $IdEscola;
    protected $Municipio;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param mixed $Id
     */
    public function setId($Id)
    {
        $this->Id = $Id;
    }

    /**
     * @return mixed
     */
    public function getUf()
    {
        return $this->Uf;
    }

    /**
     * @param mixed $Uf
     */
    public function setUf($Uf)
    {
        $this->Uf = $Uf;
    }

    /**
     * @return mixed
     */
    public function getNomeEscola()
    {
        return $this->NomeEscola;
    }

    /**
     * @param mixed $NomeEscola
     */
    public function setNomeEscola($NomeEscola)
    {
        $this->NomeEscola = $NomeEscola;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * @param mixed $Status
     */
    public function setStatus($Status)
    {
        $this->Status = $Status;
    }

    /**
     * @return mixed
     */
    public function getDataHora()
    {
        return $this->DataHora;
    }

    /**
     * @param mixed $DataHora
     */
    public function setDataHora($DataHora)
    {
        $this->DataHora = $DataHora;
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
    public function getIdEscola()
    {
        return $this->IdEscola;
    }

    /**
     * @param mixed $IdEscola
     */
    public function setIdEscola($IdEscola)
    {
        $this->IdEscola = $IdEscola;
    }

    /**
     * @return mixed
     */
    public function getMunicipio()
    {
        return $this->Municipio;
    }

    /**
     * @param mixed $Municipio
     */
    public function setMunicipio($Municipio)
    {
        $this->Municipio = $Municipio;
    }


}