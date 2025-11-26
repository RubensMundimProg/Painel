<?php

namespace RiskManager\ERM\Form;

use Estrutura\Form\AbstractForm;

class Risks extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('risks');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('risks', $this, $this->inputFilter);
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->text('description')->required(true)->label('Descrição');
        $objForm->text('type')->required(true)->label('Tipo');
        $objForm->text('category')->required(true)->label('Categoria');
        $objForm->text('riskOwner')->required(true)->label('Proprietário');
        $objForm->text('impact')->required(true)->label('Impacto');
        $objForm->text('residualImpact')->required(true)->label('Impacto Residual');
//        $objForm->text('inherentRiskScore')->required(true)->label('Pontuação de Risco Inerente');
//        $objForm->text('residualInherentRiskScore')->required(true)->label('Pontuação de Risco Residual Inerente');
        $objForm->text('probability')->required(true)->label('Probabilidade');
        $objForm->text('residualProbability')->required(true)->label('Probabilidade Residual');
        $objForm->text('controls')->required(true)->label('Controles');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
