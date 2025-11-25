<?php

namespace RiskManager\Organization\Model;

/**
 *
 * Classe Model que gerencia os campos dos grupos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Model
 */
class Group {

    public $campos = [
        'Id'=>'id',
        'Name'=>'name',
        'Description'=>'description',
        'Email'=>'email',
        'AdditionalInformation'=>'additionalInformation',
        'ResponsibleId'=>'responsibleId',
        'ResponsibleName'=>'responsibleName',
        'ResponsibleEmail'=>'responsibleEmail',
        'ResponsiblePhone'=>'responsiblePhone',
        'Responsible'=>'responsible'
    ];

}
