<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Acesso extends AbstractEstruturaService{

    protected $Id;
    protected $Usuario;
    protected $Ip;
    protected $Navegador;
    protected $DataAcesso;

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
    public function getUsuario()
    {
        return $this->Usuario;
    }

    /**
     * @param mixed $Usuario
     */
    public function setUsuario($Usuario)
    {
        $this->Usuario = $Usuario;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->Ip;
    }

    /**
     * @param mixed $Ip
     */
    public function setIp($Ip)
    {
        $this->Ip = $Ip;
    }

    /**
     * @return mixed
     */
    public function getNavegador()
    {
        return $this->Navegador;
    }

    /**
     * @param mixed $Navegador
     */
    public function setNavegador($Navegador)
    {
        $this->Navegador = $Navegador;
    }

    /**
     * @return mixed
     */
    public function getDataAcesso()
    {
        return $this->DataAcesso;
    }

    /**
     * @param mixed $DataAcesso
     */
    public function setDataAcesso($DataAcesso)
    {
        $this->DataAcesso = $DataAcesso;
    }
}