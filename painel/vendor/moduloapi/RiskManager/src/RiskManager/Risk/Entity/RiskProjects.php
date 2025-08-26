<?php

namespace RiskManager\Risk\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Projetos de Risco.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Risk\Entity
 */
class RiskProjects extends AbstractApiService {

    protected $id;
    protected $code;
    protected $name;
    protected $description;
    protected $additionalInformation;
    protected $status;
    protected $statusCode;
    protected $createdOn;
    protected $updatedOn;
    protected $closedOn;
    protected $analysisStart;
    protected $analysisEnd;
    protected $author;
    protected $leader;
    protected $substituteLeader;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAdditionalInformation()
    {
        return $this->additionalInformation;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    public function getClosedOn()
    {
        return $this->closedOn;
    }

    public function getAnalysisStart()
    {
        return $this->analysisStart;
    }

    public function getAnalysisEnd()
    {
        return $this->analysisEnd;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getLeader()
    {
        return $this->leader;
    }

    public function getSubstituteLeader()
    {
        return $this->substituteLeader;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setAdditionalInformation($additionalInformation)
    {
        $this->additionalInformation = $additionalInformation;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;
    }

    public function setClosedOn($closedOn)
    {
        $this->closedOn = $closedOn;
    }

    public function setAnalysisStart($analysisStart)
    {
        $this->analysisStart = $analysisStart;
    }

    public function setAnalysisEnd($analysisEnd)
    {
        $this->analysisEnd = $analysisEnd;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setLeader($leader)
    {
        $this->leader = $leader;
    }

    public function setSubstituteLeader($substituteLeader)
    {
        $this->substituteLeader = $substituteLeader;
    }

}
