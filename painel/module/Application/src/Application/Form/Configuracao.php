<?php

namespace Application\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Configuracao extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('Configuracao');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('configuracao',$this,$this->inputFilter);

        $objForm->text('Exame')->required(true)->label("Exame")->setAttribute('placeholder', 'Digite o exame');

        $anoAtual = date('Y');
        $anoInicio = $anoAtual-8;
        $anos = [];
        for ($anoInicio; $anoInicio <= $anoAtual; $anoInicio++) {
            $anos[$anoInicio] = $anoInicio;
        }

        $objForm->select('Ano', $anos)->required(true);

        $objForm->datepicker('DataInicio')->required(true)->label('Data Inicial')->setAttribute('placeholder', 'Informe a data de Inicio');
        $objForm->datepicker('DataFim')->required(true)->label('Data Final')->setAttribute('placeholder', 'Informe a data de Termino');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 