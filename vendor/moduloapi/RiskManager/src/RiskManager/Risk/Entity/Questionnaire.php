<?php

namespace RiskManager\Risk\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Questionarios.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Risk\Entity
 */
class Questionnaire extends AbstractApiService {

    protected $id;
    protected $name;
    protected $description;
    protected $deleted;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

}
