<?php

namespace Application\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class UltimaMilha extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('UltimaMilha');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('UltimaMilha',$this,$this->inputFilter);

        $objForm->hidden("Id")->required(false)->label("Código");
        $objForm->integer('Ac')->required(true)->label("AC");
        $objForm->integer('Al')->required(true)->label("AL");
        $objForm->integer('Ap')->required(true)->label("AP");
        $objForm->integer('Am')->required(true)->label("AM");
        $objForm->integer('Ba')->required(true)->label("BA");
        $objForm->integer('Ce')->required(true)->label("CE");
        $objForm->integer('Df')->required(true)->label("DF");
        $objForm->integer('Es')->required(true)->label("ES");
        $objForm->integer('Go')->required(true)->label("GO");
        $objForm->integer('Ma')->required(true)->label("MA");
        $objForm->integer('Mg')->required(true)->label("MG");
        $objForm->integer('Mt')->required(true)->label("MT");
        $objForm->integer('Ms')->required(true)->label("MS");
        $objForm->integer('Pa')->required(true)->label("PA");
        $objForm->integer('Pb')->required(true)->label("PB");
        $objForm->integer('Pe')->required(true)->label("PE");
        $objForm->integer('Pi')->required(true)->label("PI");
        $objForm->integer('Pr')->required(true)->label("PR");
        $objForm->integer('Rj')->required(true)->label("RJ");
        $objForm->integer('Rn')->required(true)->label("RN");
        $objForm->integer('Ro')->required(true)->label("RO");
        $objForm->integer('Rr')->required(true)->label("RR");
        $objForm->integer('Rs')->required(true)->label("RS");
        $objForm->integer('Sc')->required(true)->label("SC");
        $objForm->integer('Se')->required(true)->label("SE");
        $objForm->integer('Sp')->required(true)->label("SP");
        $objForm->integer('To')->required(true)->label("TO");

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 