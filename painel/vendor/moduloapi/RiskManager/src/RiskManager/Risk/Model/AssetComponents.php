<?php

namespace RiskManager\Risk\Model;

/**
 *
 * Classe Model que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Controls
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Risk\Model
 */
class AssetComponents {

    public $campos = [
        'assetComponent' => 'assetComponent',
        'asset' => 'asset',
        'perimeter' => 'perimeter',
        'knowledgeBase' => 'knowledgeBase',
        'survey' => 'survey',
        'questionnaire' => 'questionnaire',
        'analyst' => 'analyst',
        'interviewee' => 'interviewee',
        'reviewer' => 'reviewer',
    ];

}
