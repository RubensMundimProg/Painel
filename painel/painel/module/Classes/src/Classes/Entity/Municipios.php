<?php

namespace Classes\Entity;

use Estrutura\Service\AbstractEstruturaService;

class Municipios extends AbstractEstruturaService{

    protected $Id;
    protected $Nome;
    protected $Patch;

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
    public function getPatch()
    {
        return $this->Patch;
    }

    /**
     * @param mixed $Patch
     */
    public function setPatch($Patch)
    {
        $this->Patch = $Patch;
    }


}