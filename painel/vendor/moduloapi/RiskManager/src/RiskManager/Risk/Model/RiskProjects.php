<?php

namespace RiskManager\Risk\Model;

/**
 *
 * Classe Model que fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Projetos de Risco.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Risk\Model
 */
class RiskProjects {

    public $campos = [
        'Id' => 'id',
        'Code' => 'code',
        'Name' => 'name',
        'Description' => 'description',
        'AdditionalInformation' => 'additionalInformation',
        'Status' => 'status',
        'StatusCode' => 'statusCode',
        'CreatedOn' => 'createdOn',
        'UpdatedOn' => 'updatedOn',
        'ClosedOn' => 'closedOn',
        'AnalysisStart' => 'analysisStart',
        'AnalysisEnd' => 'analysisEnd',
        'Author' => 'author',
        'Leader' => 'leader',
        'SubstituteLeader' => 'substituteLeader',
    ];

}
