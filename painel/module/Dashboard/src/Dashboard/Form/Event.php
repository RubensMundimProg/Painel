<?php

namespace Dashboard\Form;

use Estrutura\Form\AbstractForm;
use Estrutura\Form\FormObject;
use Zend\InputFilter\InputFilter;

/**
 *
 * Classe Form que gerencia os campos dos ativos
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Organization\Form
 */
class Event extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('event');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('event',$this,$this->inputFilter);
        $objForm->text('title')->required(true)->label('Título');
        $objForm->text('description')->required(true)->label('Descrição');
        $objForm->text('progress')->required(false)->label('Progresso');
//        $objForm->text('urgency')->required(true)->label('Urgência');
//        $objForm->text('relevance')->required(false)->label('Relevância');
//        $objForm->text('severity')->required(false)->label('Severidade');
        $objForm->text('latitude')->required(false)->label('Latitude');
        $objForm->text('longitude')->required(false)->label('Longitude');
        $objForm->text('geolocationDescription')->required(false)->label('Geolocalização Descrição');
        $objForm->text('expectedStartDate')->required(false)->label('Data Prevista Início');
        $objForm->text('expectedEndDate')->required(false)->label('Data Prevista Término');
        $objForm->text('startDate')->required(false)->label('Data Real de Início');
        $objForm->text('endDate')->required(false)->label('Data Real de Término');
        $objForm->text('deadline')->required(false)->label('Prazo Final');
        $objForm->text('value')->required(false)->label('Valor');
        $objForm->select('notify',[''=>'Selecione','true'=>'Sim','false'=>'Não'])->required(false)->label('Enviar Notificações');
        $objForm->text('parentEvent')->required(false)->label('Evento Pai');
        $objForm->text('coordinator')->required(false)->label('Coordenador');
        $objForm->text('responsible')->required(false)->label('Responsável');
        $objForm->text('involved')->required(false)->label('Envolvidos');
        $objForm->text('firstReviewer')->required(false)->label('Primeiro Revisor');
        $objForm->text('secondReviewer')->required(false)->label('Segundo Revisor');
        $objForm->text('thirdReviewer')->required(false)->label('Terceiro Revisor');
        $objForm->file('data')->required(false)->label('Anexo');
        $objForm->text('fileName')->required(false)->label('Nome do Arquivo');
        $objForm->text('comment')->required(false)->label('Comentário');
//        $objForm->text('eventType')->required(false)->label('Tipo de Evento');
        $objForm->text('code')->required(false)->label('Código do Evento');

        $objForm->text('__deve_ser_reportado_como_alertaEnade')->required(false)->label('01 - Deve ser reportado como Alerta ?');
        $objForm->text('alerta_sgir')->required(false)->label('01 - Deve ser reportado como Alerta?');
        $objForm->text('nivel_do_alerta_saeb')->required(false)->label('01 - Nível do Alerta:');
        $objForm->text('descricao_do_risco_saeb')->required(false)->label('02 - Descrição do Risco no Alerta:');
        $objForm->text('__tipo_do_alerta_')->required(false)->label('02 - Tipo do Alerta :');
        $objForm->text('tipo_do_alerta_sgir')->required(false)->label('02 - Tipo do Alerta:');
        $objForm->text('__nivel_do_alerta_')->required(false)->label('03 - Nível do Alerta :');
        $objForm->text('ultima_atualizacao_saeb')->required(false)->label('03 - Última atualização do Alerta:');
        $objForm->text('__descricao_do_risco_no_alerta_')->required(false)->label('04 - Descrição do Risco no Alerta :');
        $objForm->text('__ultima_atualizacao_')->required(false)->label('05 - Última atualização :');
        $objForm->text('ultima_atualizacao')->required(false)->label('05 - Última atualização:');
        $objForm->text('__escopo_do_evento_')->required(false)->label('06 - Escopo do Evento :');
        $objForm->text('escopo_do_evento_sgir')->required(false)->label('06 - Escopo do Evento:');
        $objForm->text('__evento_gera_melhoria_')->required(false)->label('07 - Evento gera melhoria :');
        $objForm->text('evento_gera_melhoria_sgir')->required(false)->label('07 - Evento gera melhoria:');
        $objForm->text('etapa_simec_sgir')->required(false)->label('08 - Identificação da Etapa no SIMEC');
        $objForm->text('identificacao_dos_processos_sgir')->required(false)->label('09 - Identificação dos Processos no SIMEC');
        $objForm->text('ameacas')->required(false)->label('Ameaças');
        $objForm->text('ano_vigente')->required(false)->label('Ano');
        $objForm->text('area_responsavel')->required(false)->label('Área Responsável');
        $objForm->text('sistema')->required(false)->label('Avaliação pedagógica');
        $objForm->text('caminho_critico')->required(false)->label('Caminho Crítico');
        $objForm->text('categoria_evento')->required(false)->label('Categoria');
        $objForm->text('descricao_do_risco_no_alerta_sgir')->required(false)->label('Descrição do Risco no Alerta:');
        $objForm->text('emitir_no_relatorio')->required(false)->label('Emitir no relatório?');
        $objForm->text('evento')->required(false)->label('Evento');
        $objForm->text('municipio_de_aplicacao')->required(false)->label('Municipio de aplicação');
        $objForm->text('nivel_do_alerta_sgir')->required(false)->label('Nível do Alerta');
        $objForm->text('__origem_do_alerta')->required(false)->label('Origem do Alerta');
        $objForm->text('periodo')->required(false)->label('Período (Caso aplicável)');
        $objForm->text('tipos_ocorrencias')->required(false)->label('Tipos de Ocorrências');
        $objForm->text('usuario_cadastro')->required(false)->label('Usuário Cadastro');
        $objForm->text('usuario_triagem')->required(false)->label('Usuário Triagem');


        ///Campos de atualização
        $objForm->text('StartDate')->required(false)->label('Data de Início');

        /// Campos de Relatório (dashboard)
        $objForm->text('EventID')->required(false)->label('Código');
        $objForm->text('Status')->required(false)->label('Situação');
        $objForm->text('Title')->required(false)->label('Título');
        $objForm->text('UpdatedOn')->required(false)->label('Data de Atualização');
        $objForm->text('Coordinator')->required(false)->label('Coordenador');
        $objForm->text('Responsible')->required(false)->label('Responsável');
        $objForm->text('Created')->required(false)->label('Data de Criação');
        $objForm->text('LastProgressComment')->required(false)->label('Último Progresso');
        $objForm->text('Description')->required(false)->label('Descrição');


        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}