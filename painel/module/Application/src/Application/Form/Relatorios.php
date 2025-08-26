<?php

namespace Application\Form;

use Classes\Service\Alertas;
use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Relatorios extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('Relatorios');

        $service = new \Application\Service\Relatorios();

        $objAlerta = new Alertas();

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('Relatorios',$this,$this->inputFilter);

        $objForm->datepicker('dataInicio')->required(true)->label("Data Início");
        $objForm->datepicker('dataFim')->required(true)->label("Data Fim");

        $objForm->timeRM('horaInicio')->required(false)->label("Hora Inicio");
        $objForm->timeRM('horaFim')->required(false)->label("Hora Fim");

        $objForm->select('categoria',$objAlerta->categoriaSubCategoria(false, true)['categorias'])->required(false);
        $objForm->select('subcategoria',$objAlerta->getListaSubCategoria())->required(false);
        $objForm->select('uf', $service->getPermissionUfs())->required(false)->label("UF");


        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 