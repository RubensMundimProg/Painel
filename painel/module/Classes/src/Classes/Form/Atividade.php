<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Atividade extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('atividade');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('atividade',$this,$this->inputFilter);
        $objForm->hidden('Id')->required(false)->label('');
        $objForm->selectRm('sistema',false,'Sistema')->required(false)->label('');
        $objForm->text('Descricao')->required(true)->label('');
        $objForm->datevalid('Data')->required(false)->label('');
        $objForm->time('HoraInicio')->required(false)->label('');
        $objForm->time('HoraFim')->required(false)->label('');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}