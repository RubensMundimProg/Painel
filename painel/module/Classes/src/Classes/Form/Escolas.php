<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Escolas extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('escolas');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('escolas',$this,$this->inputFilter);
        $objForm->text("Id")->required(false)->label("Código");

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}