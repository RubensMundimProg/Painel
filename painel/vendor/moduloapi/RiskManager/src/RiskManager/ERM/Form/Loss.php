<?php

namespace RiskManager\ERM\Form;

use Estrutura\Form\AbstractForm;

/**
 * 
 * Classe Form que fornece orientações sobre como acessar as funcionalidades 
 * do módulo de ERM Loss
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage ERM\Form
 */
class Loss extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('loss');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('loss', $this, $this->inputFilter);
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->text('lossEventResponsible')->required(true)->label('Responsável');
        $objForm->text('value ')->required(true)->label('Valor');
        $objForm->text('accountingDate ')->required(true)->label('Data de computação');
        $objForm->text('type ')->required(true)->label('Tipo');
        $objForm->text('description')->required(false)->label('Descrição');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
