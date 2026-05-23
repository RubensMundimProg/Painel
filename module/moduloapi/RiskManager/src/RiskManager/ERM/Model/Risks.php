<?php

namespace RiskManager\ERM\Model;

/**
 *
 * Classe Model que gerencia os campos dos ativos
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage ERM\Model
 */
class Risks {

    public $campos = [
        'name' => 'Name',
        'description' => 'Description',
        'type ' => 'Type',
        'category' => 'Category',
        'riskOwner' => 'RiskOwner',
        'impact' => 'Impact',
        'residualImpact' => 'ResidualImpact',
        'inherentRiskScore' => 'InherentRiskScore',
        'residualInherentRiskScore' => 'ResidualInherentRiskScore',
        'probability' => 'Probability',
        'residualProbability' => 'ResidualProbability',
        'controls' => 'Controls',
        'deleted' => 'Deleted',
    ];

}
