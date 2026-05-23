<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Laminas\InputFilter\InputFilter;

class Alertas extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('alertas');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('alertas',$this,$this->inputFilter);
        $objForm->text("Id")->required(false)->label("Código");

        $this->formObject = $objForm;
    }

    public function getInputFilter(): \Laminas\InputFilter\InputFilterInterface
    {
        return $this->inputFilter;
    }
}
