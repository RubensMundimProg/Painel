<?php

namespace Application\Form;

use Classes\Service\Alertas;
use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class ApiTriagem extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('Triagem');

        $objAlerta = new Alertas();

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('triagem',$this,$this->inputFilter);

        $objForm->hidden("Id")->required(false)->label("Código");
        $objForm->text('municipio')->required(true)->label("Município")->setAttribute('placeholder', 'Digite o Município')->setAttribute('id', 'autocomplete-municipio');
        $objForm->hidden('uf')->required(true)->label("UF")->setAttribute('id', 'autocomplete-uf')->setAttribute('readonly', 'readonly');
        $objForm->select('coordenacao',[],false,true)->required(false)->label("Coordenação")->setAttribute('id', 'autocomplete-coordenacao')->setAttribute('data-placeholder',' ');
//        $objForm->selectRm('categoria_evento', false, 'categoria')->required(true);
        $objForm->select('categoria', $objAlerta->categoriaSubCategoria()['categorias'])->required(true);
        $objForm->select('subcategoria', [])->required(false);
        $objForm->select('impacto_aplicacao',[''=>'Selecione','Não'=>'Não','Sim'=>'Sim'])->required(false)->label("Impacto na Aplicação?")->setAttribute('disabled', true);
        $objForm->text('nro_processo')->required(false)->label("N° Processo")->setAttribute('disabled', true);
        $objForm->hidden('titulo')->required(true)->label('Titulo')->setAttribute('readonly', 'readonly');
        $objForm->textarea('descricao')->required(true)->label('Descricao')->setAttribute('placeholder', 'Digite a Descrição')->setAttribute('maxlength',255);
        $objForm->textarea('providencias_adotadas')->required(false)->label('Providências Adotadas')->setAttribute('placeholder', '')->setAttribute('maxlength',255);
        $objForm->file('anexo',true)->required(false)->label('Anexo');

        $objForm->text('codigo_origem')->required(false);
        $objForm->text('nome_usuario')->required(true);
        $objForm->text('email_usuario')->required(true);

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 