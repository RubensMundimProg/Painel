<?php

namespace RiskManager\CustomObjects\Model;

/**
 *
 * Classe Model que gerencia os campos dos objetos customizados
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 1.0
 * @access public
 * @package RiskManager
 * @subpackage CustomObjects\Model
 */
class CustomObjects {

    public $campos = [
        'id' => 'Id',
        'deleted' => 'Deleted',
        'name' => 'Name',
        'dataCreated' => 'DateCreated',
        'dateUpdated' => 'DateUpdated',
    ];

}
