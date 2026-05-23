<?php

namespace RiskManager\CustomObjects\Form;

use Estrutura\Form\AbstractForm;

class CustomObjects extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('customObjects');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('customObjects', $this, $this->inputFilter);
        $objForm ->text('id')->required(false)->label();
        $objForm ->text('deleted')->required(false)->label();
        $objForm ->text('name')->required(false)->label();
        $objForm ->text('dataCreated')->required(false)->label();
        $objForm ->text('dateUpdated')->required(false)->label();

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
