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
class AssetComponents extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('controls');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('controls', $this, $this->inputFilter);
        $objForm->text('assetComponent')->required(true)->label('assetComponent');
        $objForm->text('asset')->required(true)->label('asset');
        $objForm->text('perimeter')->required(true)->label('perimeter');
        $objForm->text('knowledgeBase')->required(true)->label('knowledgeBase');
        $objForm->text('survey')->required(true)->label('survey');
        $objForm->text('questionnaire')->required(true)->label('questionnaire');
        $objForm->text('analyst')->required(true)->label('analyst');
        $objForm->text('interviewee')->required(true)->label('interviewee');
        $objForm->text('reviewer')->required(true)->label('reviewer');
        

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
