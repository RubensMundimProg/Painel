<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Escolas extends AbstractEstruturaService{

    protected $Id;
    protected $Nome;
    protected $Uf;
    protected $Municipio;
    protected $Latitude;
    protected $Longitude;
    protected $Endereco;

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
    public function getNome()
    {
        return $this->Nome;
    }

    /**
     * @param mixed $Nome
     */
    public function setNome($Nome)
    {
        $this->Nome = $Nome;
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
    public function getEndereco()
    {
        return $this->Endereco;
    }

    /**
     * @param mixed $Endereco
     */
    public function setEndereco($Endereco)
    {
        $this->Endereco = $Endereco;
    }




}