<?php

namespace RiskManager\Organization\Model;

/**
 *
 * Classe Model que gerencia os campos das pessoas da organização
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Model
 */
class People {

    public $campos = [
        'Id'=>'id',
        'Name'=>'name',
        'Description'=>'description',
        'Email'=>'email',
        'Phone'=>'phone',
        'AdditionalInformation'=>'additionalInformation',
        'Login'=>'login',
        'Status'=>'status',
    ];

}
