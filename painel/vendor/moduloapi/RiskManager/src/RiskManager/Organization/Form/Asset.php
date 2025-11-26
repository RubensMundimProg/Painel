<?php

namespace RiskManager\Organization\Form;

use Estrutura\Form\AbstractForm;

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
class Asset extends AbstractForm{
    public function __construct($options=[]){
        parent::__construct('asset');

        $this->inputFilter = new InputFilter();
        $objForm = new FormObject('asset',$this,$this->inputFilter);
        $objForm->text('assetType')->required(true)->label('Tipo de Ativo');
        $objForm->text('name')->required(true)->label('Nome');
        $objForm->text('path')->required(false)->label('Path');
        $objForm->text('responsible')->required(false)->label('Responsável');
        $objForm->text('relevance')->required(true)->label('Relevância');
        $objForm->text('criticality')->required(false)->label('Criticidade');
        $objForm->text('analysisFrequency')->required(false)->label('Frequencia de Análise');
        $objForm->text('description')->required(false)->label('Descrição');
        $objForm->text('latitude')->required(false)->label('Latitude');
        $objForm->text('longitude')->required(false)->label('Longitude');
        $objForm->text('geolocationDescription')->required(false)->label('Descrição de Geolocalização');
        $objForm->text('zoomLevel')->required(false)->label('Level de Zoom');
        $objForm->text('customAttributes')->required(false)->label('Atributos Customizados');
        $objForm->text('hostAddress')->required(false)->label('Endereço de Host');
        $objForm->text('credentials')->required(false)->label('Credenciais');
        $objForm->text('collectorServer')->required(false)->label('Coleção do Servidor');
        $objForm->text('netbiosName')->required(false)->label('Nome da Netbios');
        $objForm->text('ipAddress')->required(false)->label('Endereço de IP');
        $objForm->text('dnsName')->required(false)->label('Nome do DSN');

        $this->formObject = $objForm;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }
} 