<?php

namespace RiskManager\Risk\Form;

use Estrutura\Form\AbstractForm;

/**
 * 
 * Classe Form que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Controls
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Risk\Form
 */
class Questionnaire extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('controls');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('controls', $this, $this->inputFilter);
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->text('description')->required(false)->label('Descrição');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
