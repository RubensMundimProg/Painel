<?php

namespace RiskManager\Organization\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

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
class People extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('people');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('people',$this,$this->inputFilter);
        $objForm->hidden('id')->required(false)->label('');
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->textarea('description')->required(false)->label('Descrição');
        $objForm->email('email')->required(true)->label('Email');
        $objForm->telefone('phone')->required(false)->label('Telefone');
        $objForm->text('additionalInformation')->required(false)->label('Informação Adicional');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 