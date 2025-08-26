<?php

namespace Dashboard\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class AvaliacaoPedagogica extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('avaliacao-pedagogica');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('avaliacao-pedagogica',$this,$this->inputFilter);

        $sistema = new \Zend\Session\Container('SistemaSelecionado');
        $nomeSistema = $sistema->offsetGet('sistema');

        if(!$nomeSistema) $nomeSistema = '';

        $objForm->selectRm('sistema',false,'AvaliacaoPedagogica')->required(false)->value($nomeSistema)->label('');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}