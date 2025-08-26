<?php

namespace Classes\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Aviso extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('alertas');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('alertas',$this,$this->inputFilter);
        $objForm->hidden('Id')->required(false)->label('');
        $objForm->selectRm('sistema',false,'AvaliacaoPedagogica')->required(true)->label('');
        $objForm->text('Titulo')->required(true)->label('');
        $objForm->file('Anexo')->required(false)->label('');
        $objForm->textarea('Texto')->required(false)->label('');
        $objForm->datevalid('DataInicio')->required(true)->setAttribute('data-validate','<=,DataFim')->label('');
        $objForm->time('HoraInicio')->required(false)->label('');
        $objForm->datevalid('DataFim')->required(true)->setAttribute('data-validate','>=,DataInicio')->label('');
        $objForm->time('HoraFim')->required(false)->label('');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}