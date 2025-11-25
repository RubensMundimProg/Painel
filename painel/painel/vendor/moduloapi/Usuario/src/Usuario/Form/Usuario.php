<?php

namespace Usuario\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Usuario extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('usuario');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('usuario',$this,$this->inputFilter);
        $objForm->hidden('id')->required(false);
        $objForm->text('nome')->required(true)->label('Nome');
        $objForm->password('senha')->required(true)->label('senha');
        $objForm->email('email')->required(true)->label('Email');
        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}