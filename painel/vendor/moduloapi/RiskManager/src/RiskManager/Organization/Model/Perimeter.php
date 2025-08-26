<?php

namespace RiskManager\Organization\Model;

/**
 *
 * Classe Model que gerencia os campos dos perimetros
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Model
 */
class Perimeter {

    public $campos = [
        'Id'=>'Id',
        'Name'=>'Name',
        'Path'=>'Path',
        'Description'=>'Description',
        'Longitude'=>'Longitude',
        'Latitude'=>'Latitude',
        'GeolocationDescription'=>'GeolocationDescription',
        'AdditionalInformation'=>'AdditionalInformation',
        'ResponsibleId'=>'ResponsibleId',
        'ResponsibleName'=>'ResponsibleName',
        'ResponsibleEmail'=>'ResponsibleEmail',
        'ResponsiblePhone'=>'ResponsiblePhone',
    ];

}
