<?php

namespace RiskManager\Workflow\Form;

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
        $objForm->text('urgency')->required(true)->label('Urgência');
        $objForm->text('relevance')->required(false)->label('Relevância');
        $objForm->text('severity')->required(false)->label('Severidade');
//        $objForm->text('latitude')->required(false)->label('Latitude');
//        $objForm->text('longitude')->required(false)->label('Longitude');
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
        $objForm->text('eventType')->required(false)->label('Tipo de Evento');
        $objForm->text('code')->required(false)->label('Código do Evento');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}