<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Aviso extends AbstractEstruturaService{
    protected $Id;
    protected $AvaliacaoPedagogica;
    protected $Titulo;
    protected $Anexo;
    protected $Texto;
    protected $DataInicio;
    protected $HoraInicio;
    protected $DataFim;
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
    public function getAvaliacaoPedagogica()
    {
        return $this->AvaliacaoPedagogica;
    }

    /**
     * @param mixed $AvaliacaoPedagogica
     */
    public function setAvaliacaoPedagogica($AvaliacaoPedagogica)
    {
        $this->AvaliacaoPedagogica = $AvaliacaoPedagogica;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->Titulo;
    }

    /**
     * @param mixed $Titulo
     */
    public function setTitulo($Titulo)
    {
        $this->Titulo = $Titulo;
    }

    /**
     * @return mixed
     */
    public function getAnexo()
    {
        return $this->Anexo;
    }

    /**
     * @param mixed $Anexo
     */
    public function setAnexo($Anexo)
    {
        $this->Anexo = $Anexo;
    }

    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->Texto;
    }

    /**
     * @param mixed $Texto
     */
    public function setTexto($Texto)
    {
        $this->Texto = $Texto;
    }

    /**
     * @return mixed
     */
    public function getDataInicio()
    {
        return $this->DataInicio;
    }

    /**
     * @param mixed $DataInicio
     */
    public function setDataInicio($DataInicio)
    {
        $this->DataInicio = $DataInicio;
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
    public function getDataFim()
    {
        return $this->DataFim;
    }

    /**
     * @param mixed $DataFim
     */
    public function setDataFim($DataFim)
    {
        $this->DataFim = $DataFim;
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