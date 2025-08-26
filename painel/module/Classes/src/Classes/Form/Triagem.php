<?php

namespace Classes\Form;

use Classes\Service\Alertas;
use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class Triagem extends AbstractForm{
    public function __construct($options=[],$service = null){
        parent::__construct('triagem');

        $objAlerta = new Alertas();

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('triagem',$this,$this->inputFilter);

        $objForm->hidden("Id")->required(false)->label("Código");
        $objForm->hidden('Municipio')->required(true)->label("Município")->setAttribute('placeholder', 'Digite o Município')->setAttribute('id', 'autocomplete-municipio');
        $objForm->hidden('Uf')->required(true)->label("UF")->setAttribute('id', 'autocomplete-uf')->setAttribute('readonly', 'readonly');
//        $objForm->select('Coordenacao',(!$service)?[]:$service->getCoordenacoes(),false,true)->required(false)->label("Coordenação")->setAttribute('id', 'autocomplete-coordenacao');
        $objForm->hidden('Coordenacao')->required(false)->label("Coordenação");
        $objForm->checkbox('Categoria',$objAlerta->categoriaSubCategoria()['categorias'])->required(true);
//        $objForm->select('Categoria',$objAlerta->categoriaSubCategoria()['categorias'],true)->required(true);
        $objForm->select('SubCategoria',(!$service)?[]:$service->getListaSubCategoria())->required(false);
        $objForm->select('OrigemInformacao',[''=>'Selecione','BSI'=>'BSI','CMI'=>'CMI','RM'=>'RM','RNC'=>'RNC','SIGAS'=>'SIGAS'])->required(false);
        $objForm->datepicker('DataRegistro')->required(false);
        $objForm->select('ImpactoAplicacao',[''=>'Selecione','Não'=>'Não','Sim'=>'Sim'])->required(false)->label("Impacto na Aplicação?")->setAttribute('disabled', true);
        $objForm->selectRm('dia_da_aplicacao',false,'DiaAplicacao')->label('Dia da aplicação')->required(false);
        $objForm->text('NroProcesso')->required(false)->label("N° Processo")->setAttribute('disabled', true);
        $objForm->hidden('Titulo')->required(true)->label('Titulo')->setAttribute('readonly', 'readonly');
        $objForm->textarea('Descricao')->required(true)->label('Descricao')->setAttribute('placeholder', 'Digite a Descrição')->setAttribute('maxlength',1000);
        $objForm->textarea('ProvidenciasAdotadas')->required(false)->label('Providências Adotadas')->setAttribute('placeholder', '')->setAttribute('maxlength',1000);
        $objForm->textarea('InformacoesAdicionais')->required(false)->label('Informações Adicionais')->setAttribute('placeholder', '')->setAttribute('maxlength',1000);
        $objForm->file('Anexo',true)->required(false)->label('Anexo');

        $objForm->selectEstados('UfFiltro')->required(false)->label('UF');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}