<?php

namespace Dashboard\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Dashboard extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('dashboard');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('dashboard',$this,$this->inputFilter);

        $objForm->datepicker('start-date')->required(false)->setAttribute('data-validate','<=,end-date')->label('');
        $objForm->datepicker('end-date')->required(false)->setAttribute('data-validate','>=,start-date')->label('');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}