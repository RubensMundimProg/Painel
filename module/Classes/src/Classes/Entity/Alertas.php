<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Alertas extends AbstractEstruturaService{

    protected $Id;
    protected $Titulo;
    protected $Municipio;
    protected $Uf;
    protected $Descricao;
    protected $DescricaoRisco;
    protected $NivelAlerta;
    protected $Categoria;
    protected $SubCategoria;
    protected $Ocorrencia;
    protected $Coordenacao;
    protected $ImpactoAplicacao;
    protected $NroProcesso;
    protected $Anexo;
    protected $Usuario;
    protected $UsuarioTriagem;
    protected $DataHora;
    protected $Status;
    protected $OrigemInformacao;
    protected $ProvidenciasAdotadas;
    protected $InformacoesAdicionais;
    protected $CodigoOrigem;
    protected $CodigoRm;
    protected $Sistema;
    protected $DiaAplicacao;
    protected $Ano;

    /**
     * @return mixed
     */
    public function getAno()
    {
        return $this->Ano;
    }

    /**
     * @param mixed $Ano
     */
    public function setAno($Ano)
    {
        $this->Ano = $Ano;
    }

    /**
     * @return mixed
     */
    public function getDiaAplicacao()
    {
        return $this->DiaAplicacao;
    }

    /**
     * @param mixed $DiaAplicacao
     */
    public function setDiaAplicacao($DiaAplicacao)
    {
        $this->DiaAplicacao = $DiaAplicacao;
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
    public function getCodigoRm()
    {
        return $this->CodigoRm;
    }

    /**
     * @param mixed $CodigoRm
     */
    public function setCodigoRm($CodigoRm)
    {
        $this->CodigoRm = $CodigoRm;
    }

    /**
     * @return mixed
     */
    public function getCodigoOrigem()
    {
        return $this->CodigoOrigem;
    }

    /**
     * @param mixed $CodigoOrigem
     */
    public function setCodigoOrigem($CodigoOrigem)
    {
        $this->CodigoOrigem = $CodigoOrigem;
    }

    /**
     * @return mixed
     */
    public function getOrigemInformacao()
    {
        return $this->OrigemInformacao;
    }

    /**
     * @param mixed $OrigemInformacao
     */
    public function setOrigemInformacao($OrigemInformacao)
    {
        $this->OrigemInformacao = $OrigemInformacao;
    }

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
    public function getDescricaoRisco()
    {
        return $this->DescricaoRisco;
    }

    /**
     * @param mixed $DescricaoRisco
     */
    public function setDescricaoRisco($DescricaoRisco)
    {
        $this->DescricaoRisco = $DescricaoRisco;
    }

    /**
     * @return mixed
     */
    public function getNivelAlerta()
    {
        return $this->NivelAlerta;
    }

    /**
     * @param mixed $NivelAlerta
     */
    public function setNivelAlerta($NivelAlerta)
    {
        $this->NivelAlerta = $NivelAlerta;
    }

    /**
     * @return mixed
     */
    public function getCategoria()
    {
        return $this->Categoria;
    }

    /**
     * @param mixed $Categoria
     */
    public function setCategoria($Categoria)
    {
        $this->Categoria = $Categoria;
    }

    /**
     * @return mixed
     */
    public function getSubCategoria()
    {
        return $this->SubCategoria;
    }

    /**
     * @param mixed $SubCategoria
     */
    public function setSubCategoria($SubCategoria)
    {
        $this->SubCategoria = $SubCategoria;
    }


    /**
     * @return mixed
     */
    public function getOcorrencia()
    {
        return $this->Ocorrencia;
    }

    /**
     * @param mixed $Ocorrencia
     */
    public function setOcorrencia($Ocorrencia)
    {
        $this->Ocorrencia = $Ocorrencia;
    }

    /**
     * @return mixed
     */
    public function getCoordenacao()
    {
        return $this->Coordenacao;
    }

    /**
     * @param mixed $Coordenacao
     */
    public function setCoordenacao($Coordenacao)
    {
        $this->Coordenacao = $Coordenacao;
    }

    /**
     * @return mixed
     */
    public function getImpactoAplicacao()
    {
        return $this->ImpactoAplicacao;
    }

    /**
     * @param mixed $ImpactaAplicacao
     */
    public function setImpactoAplicacao($ImpactoAplicacao)
    {
        $this->ImpactoAplicacao = $ImpactoAplicacao;
    }

    /**
     * @return mixed
     */
    public function getNroProcesso()
    {
        return $this->NroProcesso;
    }

    /**
     * @param mixed $NroProcesso
     */
    public function setNroProcesso($NroProcesso)
    {
        $this->NroProcesso = $NroProcesso;
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
    public function getUsuarioTriagem()
    {
        return $this->UsuarioTriagem;
    }

    /**
     * @param mixed $UsuarioTriagem
     */
    public function setUsuarioTriagem($UsuarioTriagem)
    {
        $this->UsuarioTriagem = $UsuarioTriagem;
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
    public function getProvidenciasAdotadas()
    {
        return $this->ProvidenciasAdotadas;
    }

    /**
     * @param mixed $ProvidenciasAdotadas
     */
    public function setProvidenciasAdotadas($ProvidenciasAdotadas)
    {
        $this->ProvidenciasAdotadas = $ProvidenciasAdotadas;
    }

    /**
     * @return mixed
     */
    public function getInformacoesAdicionais()
    {
        return $this->InformacoesAdicionais;
    }

    /**
     * @param mixed $InformacoesAdicionais
     */
    public function setInformacoesAdicionais($InformacoesAdicionais)
    {
        $this->InformacoesAdicionais = $InformacoesAdicionais;
    }

}