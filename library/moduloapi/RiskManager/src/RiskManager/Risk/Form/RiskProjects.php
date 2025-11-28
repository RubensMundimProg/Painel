<?php

namespace RiskManager\Risk\Form;

use Estrutura\Form\AbstractForm;

/**
 * 
 * Classe Form que fornece orientações sobre como acessar as funcionalidades do módulo de Riscos, Projetos de Risco.
 *
 * @author Ronaldo Melo <ronaldo.melo@modulo.com>
 * @version 0.1 
 * @access public  
 * @package RiskManager 
 * @subpackage Risk\Form
 */
class RiskProjects extends AbstractForm {

    public function __construct($options = [])
    {
        parent::__construct('riskprojects');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('riskprojects', $this, $this->inputFilter);
        $objForm->text('id')->required(true)->label('Id');
        $objForm->text('code')->required(false)->label('Código');
        $objForm->text('name')->required(false)->label('Nome');
        $objForm->text('description')->required(false)->label('Descrição');
        $objForm->text('additionalInformation')->required(false)->label('Informações adicionais');
        $objForm->text('status')->required(false)->label('Situação');
        $objForm->text('statusCode')->required(false)->label('Código da situação');
        $objForm->text('createdOn')->required(false)->label('Data de criação');
        $objForm->text('updatedOn')->required(false)->label('Data de atualização');
        $objForm->text('closedOn')->required(false)->label('Data de fechamento');
        $objForm->text('analysisStart')->required(false)->label('Data de inicio da análise');
        $objForm->text('analysisEnd')->required(false)->label('Data de fim da análise');
        $objForm->text('author')->required(false)->label('Autor');
        $objForm->text('leader')->required(false)->label('Líder');
        $objForm->text('substituteLeader')->required(false)->label('Líder substituto');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
