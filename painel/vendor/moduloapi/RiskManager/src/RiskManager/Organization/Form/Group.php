<?php

namespace RiskManager\Organization\Form;

use Estrutura\Form\AbstractForm;

/**
 *
 * Classe Form que gerencia os campos dos grupos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Form
 */
class Group extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('group');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('group',$this,$this->inputFilter);
        $objForm->text('id')->required(true)->label('Código');
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->text('description')->required(true)->label('Descrição');
        $objForm->text('email')->required(true)->label('Email');
        $objForm->text('additionalInformation')->required(true)->label('Informações Adicionais');
        $objForm->text('responsibleId')->required(true)->label('Código do Responsável');
        $objForm->text('responsibleName')->required(true)->label('Nome do Responsável');
        $objForm->text('responsibleEmail')->required(true)->label('Email do Responsável');
        $objForm->text('responsiblePhone')->required(true)->label('Telefone do Responsável');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 