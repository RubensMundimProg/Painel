<?php

namespace RiskManager\Organization\Form;

use Estrutura\Form\AbstractForm;

/**
 *
 * Classe Form que gerencia os campos dos ativos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Form
 */
class Perimeter extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('asset');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('perimeter',$this,$this->inputFilter);
        $objForm->text('Id')->required(false)->label('Id');
        $objForm->text('Name')->required(true)->label('Name');
        $objForm->text('Path')->required(false)->label('Path');
        $objForm->text('Description')->required(false)->label('Description');
        $objForm->text('Longitude')->required(false)->label('Longitude');
        $objForm->text('Latitude')->required(false)->label('Latitude');
        $objForm->text('GeolocationDescription')->required(false)->label('GeolocationDescription');
        $objForm->text('AdditionalInformation')->required(false)->label('AdditionalInformation');
        $objForm->text('ResponsibleId')->required(false)->label('ResponsibleId');
        $objForm->text('ResponsibleName')->required(false)->label('ResponsibleName');
        $objForm->text('ResponsibleEmail')->required(false)->label('ResponsibleEmail');
        $objForm->text('ResponsiblePhone')->required(false)->label('ResponsiblePhone');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 