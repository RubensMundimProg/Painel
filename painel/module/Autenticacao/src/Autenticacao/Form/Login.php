<?php

namespace Autenticacao\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Login extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('Triagem');


        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('triagem',$this,$this->inputFilter);

        $objForm->hidden("Id")->required(false)->label("Código");
        $objForm->text('login')->required(true)->label("Login");
        $objForm->password('password')->required(true)->label("Senha");


        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 