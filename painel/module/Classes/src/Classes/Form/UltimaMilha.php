<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class UltimaMilha extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('ultima_milha');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('ultima_milha',$this,$this->inputFilter);
        $objForm->text("Id")->required(false)->label("Código");
        $objForm->text('NOME_ESCOLA')->required(false)->label('Nome Escola');
        $objForm->text('STATUS')->required(false)->label('Situação');
        $objForm->text('MUNICIPIO')->required(false)->label('Município');


        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}