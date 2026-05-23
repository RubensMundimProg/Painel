<?php

namespace Modulo\Form;

class UsuarioApi {
    public function __construct($options=[]){
        parent::__construct('usuario');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('usuario',$this,$this->inputFilter);
        $objForm->password('senha')->required(true)->label('Senha');
        $objForm->email('email')->required(true)->label('Email');
        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 