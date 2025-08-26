<?php

namespace Application\Form;

use Classes\Service\Alertas;
use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

class OrdemServico extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('OrdemServico');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('OrdemServico',$this,$this->inputFilter);

        $objForm->text('name')->required(true)->label('Nome da Os');
        $objForm->dateRM('data_de_inicio')->required(true)->label('Data de Início');
        $objForm->dateRM('data_de_termino')->required(true)->label('Data de Término');
        $objForm->number('servico_s1')->required(false)->label('S1 - Desenvolvimento de Plano de Comunicação e Consulta Interno e Externo');
        $objForm->number('servico_s2')->required(false)->label('S2 - Definição do contexto considerando variáveis internas e externas relativas ao processo analisado');
        $objForm->number('servico_s3')->required(false)->label('S3 - Identificação dos fatores de risco que podem ter impacto material sobre o processo analisado');
        $objForm->number('servico_s4')->required(false)->label('S4 - Análise dos riscos identificados');
        $objForm->number('servico_s5')->required(false)->label('S5 - Avaliação dos riscos identificados');
        $objForm->number('servico_s6')->required(false)->label('S6 - Elaboração do plano de tratamento do risco avaliado');
        $objForm->number('servico_s7')->required(false)->label('S7 - Realização do monitoramento de riscos identificados, análise crítica dos resultados alcançados e eficácia do tratamento recomendado');
        $objForm->number('servico_s8')->required(false)->label('S8 - Assessoramento especializado e apoio à equipe interna nas atividades de monitoramento dos processos e da gestão de riscos');
        $objForm->select('situacao',['Em Execução'=>'Em Execução','Finalizado'=>'Finalizado','Projeção'=>'Projeção'])->required(true)->label('Situacao');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 