<?php

namespace RiskManager\Risk\Entity;

use Base\Service\AbstractApiService;

/**
 * 
 * Classe Entity que fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Componentes de Ativo.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Risk\Entity
 */
class AssetComponents extends AbstractApiService {

    protected $assetComponent;
    protected $asset;
    protected $perimeter;
    protected $knowledgeBase;
    protected $survey;
    protected $questionnaire;
    protected $analyst;
    protected $interviewee;
    protected $reviewer;

    public function getAssetComponent()
    {
        return $this->assetComponent;
    }

    public function getAsset()
    {
        return $this->asset;
    }

    public function getPerimeter()
    {
        return $this->perimeter;
    }

    public function getKnowledgeBase()
    {
        return $this->knowledgeBase;
    }

    public function getSurvey()
    {
        return $this->survey;
    }

    public function getQuestionnaire()
    {
        return $this->questionnaire;
    }

    public function getAnalyst()
    {
        return $this->analyst;
    }

    public function getInterviewee()
    {
        return $this->interviewee;
    }

    public function getReviewer()
    {
        return $this->reviewer;
    }

    public function setAssetComponent($assetComponent)
    {
        $this->assetComponent = $assetComponent;
    }

    public function setAsset($asset)
    {
        $this->asset = $asset;
    }

    public function setPerimeter($perimeter)
    {
        $this->perimeter = $perimeter;
    }

    public function setKnowledgeBase($knowledgeBase)
    {
        $this->knowledgeBase = $knowledgeBase;
    }

    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    public function setQuestionnaire($questionnaire)
    {
        $this->questionnaire = $questionnaire;
    }

    public function setAnalyst($analyst)
    {
        $this->analyst = $analyst;
    }

    public function setInterviewee($interviewee)
    {
        $this->interviewee = $interviewee;
    }

    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;
    }

}
