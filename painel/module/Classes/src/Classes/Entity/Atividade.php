<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Atividade extends AbstractEstruturaService{
    protected $Id;
    protected $Sistema;
    protected $Descricao;
    protected $Data;
    protected $HoraInicio;
    protected $HoraFim;

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
    public function getSistema()
    {
        return $this->Sistema;
    }

    /**
     * @param mixed $Sistema
     */
    public function setSistema($Sistema)
    {
        $this->Sistema = $Sistema;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->Descricao;
    }

    /**
     * @param mixed $Descricao
     */
    public function setDescricao($Descricao)
    {
        $this->Descricao = $Descricao;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->Data;
    }

    /**
     * @param mixed $Data
     */
    public function setData($Data)
    {
        $this->Data = $Data;
    }

    /**
     * @return mixed
     */
    public function getHoraInicio()
    {
        return $this->HoraInicio;
    }

    /**
     * @param mixed $HoraInicio
     */
    public function setHoraInicio($HoraInicio)
    {
        $this->HoraInicio = $HoraInicio;
    }

    /**
     * @return mixed
     */
    public function getHoraFim()
    {
        return $this->HoraFim;
    }

    /**
     * @param mixed $HoraFim
     */
    public function setHoraFim($HoraFim)
    {
        $this->HoraFim = $HoraFim;
    }


}