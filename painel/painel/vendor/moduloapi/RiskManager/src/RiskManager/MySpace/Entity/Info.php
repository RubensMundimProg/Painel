<?php

namespace RiskManager\MySpace\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que armazena informações sobre a versão do 
 * Módulo Risk Manager que está sendo acessada.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage MySpace\Entity
 */
class Info extends AbstractApiService {

    /**
     *
     * @var string 
     */
    protected $version;

    /**
     *
     * @var string 
     */
    protected $riskManagerUrl;

    /**
     *
     * @var string 
     */
    protected $workflowServicesUrl;

    /**
     *
     * 
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * 
     * @return mixed
     */
    public function getRiskManagerUrl()
    {
        return $this->riskManagerUrl;
    }

    /**
     * 
     * @return mixed
     */
    public function getWorkflowServicesUrl()
    {
        return $this->workflowServicesUrl;
    }

    /**
     * 
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * 
     * @param mixed $riskManagerUrl
     */
    public function setRiskManagerUrl($riskManagerUrl)
    {
        $this->riskManagerUrl = $riskManagerUrl;
    }

    /**
     * 
     * @param mixed $workflowServicesUrl
     */
    public function setWorkflowServicesUrl($workflowServicesUrl)
    {
        $this->workflowServicesUrl = $workflowServicesUrl;
    }

}
