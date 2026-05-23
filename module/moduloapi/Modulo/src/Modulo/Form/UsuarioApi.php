<?php

namespace Modulo\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Laminas\InputFilter\InputFilter;

class UsuarioApi extends AbstractForm
{
    public function __construct($options = [])
    {
        parent::__construct('usuario');

        $this->inputFilter = new InputFilter();

        $objForm = new FormObject('usuario', $this, $this->inputFilter);
        $objForm->password('senha')->required(true)->label('Senha');
        $objForm->email('email')->required(true)->label('Email');

        $this->formObject = $objForm;
    }

    public function getInputFilter(): \Laminas\InputFilter\InputFilterInterface
    {
        return $this->inputFilter;
    }
}