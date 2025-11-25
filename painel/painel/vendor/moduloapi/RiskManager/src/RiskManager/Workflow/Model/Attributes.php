<?php

namespace RiskManager\Workflow\Model;

/**
 *
 * Classe Model que gerencia os campos dos Attributos Customizados
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Workflow\Model
 */
class Attributes {

    public $campos = [
        'Name'=>'name',
        'VariableName'=>'variableName',
        'Description'=>'description',
        'TypeName'=>'typeName',
        'MinLength'=>'minLength',
        'MaxLength'=>'maxLength',
        'FieldMask'=>'fieldMask',
        'MinValue'=>'minValue',
        'MaxValue'=>'maxValue',
        'DecimalPlaces'=>'decimalPlaces',
        'ApplyAlphabeticalOrder'=>'applyAlphabeticalOrder',
        'AllowedValues'=>'allowedValues',
        'AllowedItems'=>'allowedItems'
    ];

}
