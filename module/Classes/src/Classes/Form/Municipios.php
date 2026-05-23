<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Laminas\InputFilter\InputFilter;

class Municipios extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('municipios');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('municipios',$this,$this->inputFilter);
        $objForm->text("Id")->required(false)->label("Código");

        $this->formObject = $objForm;
    }

    public function getInputFilter(): \Laminas\InputFilter\InputFilterInterface
    {
        return $this->inputFilter;
    }
}
