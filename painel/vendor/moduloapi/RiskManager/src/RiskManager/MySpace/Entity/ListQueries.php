<?php

namespace RiskManager\MySpace\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena uma lista com todas as consultas a que o usuário tem acesso.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class ListQueries extends AbstractApiService {

    /**
     *
     * @var string 
     */
    protected $name;

    /**
     *
     * @var string 
     */
    protected $description;

    /**
     *
     * @var Guid 
     */
    protected $id;

    /**
     *
     * @var string 
     */
    protected $type;

    /**
     *
     * @var DateTime 
     */
    protected $updatedOn;

    /**
     *
     * @var object 
     */
    protected $links;

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setId(Guid $id)
    {
        $this->id = $id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setUpdatedOn(DateTime $updatedOn)
    {
        $this->updatedOn = $updatedOn;
    }

    public function setLinks($links)
    {
        $this->links = $links;
    }

}
