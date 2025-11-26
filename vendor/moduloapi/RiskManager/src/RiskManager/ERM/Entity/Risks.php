<?php

namespace RiskManager\ERM\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações dos ativos
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage ERM\Entity
 */
class Risks extends AbstractApiService {

    protected $id;
    protected $name;
    protected $description;
    protected $type;
    protected $category;
    protected $riskOwner;
    protected $impact;
    protected $residualImpact;
    protected $inherentRiskScore;
    protected $residualInherentRiskScore;
    protected $probability;
    protected $residualProbability;
    protected $controls;
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

    public function getType()
    {
        return $this->type;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getRiskOwner()
    {
        return $this->riskOwner;
    }

    public function getImpact()
    {
        return $this->impact;
    }

    public function getResidualImpact()
    {
        return $this->residualImpact;
    }

    public function getInherentRiskScore()
    {
        return $this->inherentRiskScore;
    }

    public function getResidualInherentRiskScore()
    {
        return $this->residualInherentRiskScore;
    }

    public function getProbability()
    {
        return $this->probability;
    }

    public function getResidualProbability()
    {
        return $this->residualProbability;
    }

    public function getControls()
    {
        return $this->controls;
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

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setRiskOwner($riskOwner)
    {
        $this->riskOwner = $riskOwner;
    }

    public function setImpact($impact)
    {
        $this->impact = $impact;
    }

    public function setResidualImpact($residualImpact)
    {
        $this->residualImpact = $residualImpact;
    }

    public function setInherentRiskScore($inherentRiskScore)
    {
        $this->inherentRiskScore = $inherentRiskScore;
    }

    public function setResidualInherentRiskScore($residualInherentRiskScore)
    {
        $this->residualInherentRiskScore = $residualInherentRiskScore;
    }

    public function setProbability($probability)
    {
        $this->probability = $probability;
    }

    public function setResidualProbability($residualProbability)
    {
        $this->residualProbability = $residualProbability;
    }

    public function setControls($controls)
    {
        $this->controls = $controls;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

}
