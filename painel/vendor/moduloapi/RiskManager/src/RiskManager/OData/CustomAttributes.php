<?php

namespace RiskManager\OData;

/**
 *
 * Classe que gerencia os atributos customizados dos elementos de API
 *
 * @author Bruno Silva <bruno.silva@modulo.com>
 * @version 0.1
 * @access public
 * @package RiskManager
 * @subpackage Odata
 */
class CustomAttributes {
    protected $attributes = [
        'descricao_do_risco_no_alerta_sgir',
        'nivel_do_alerta_sgir',
        'sistema',
        'ano_vigente',
        'caminho_critico',
        '__origem_do_alerta',
        'categoria_evento',
        'nro_processo',
        'impacto_aplicacao',
        'coordenacao',
        'municipio_de_aplicacao',
        'unidade_federativa',
        'status_tratamento',
        'origem_informacao',
        'usuario_triagem',
        'usuario_cadastro',
        'tipos_ocorrencias',
        'evento',
        'anexar_arquivos',
        'providencias_adotadas',
        'situacao_sistema_origem',
        'dia_da_aplicacao',
        'etapas_do_cronograma',
    ];
    protected $campos = [];

    /**
     * Seta os campos de acordo com os atributos mapeados
     * @param $array
     */
    public function exchangeArray($array){
        foreach($array as $chave => $item){
            if(in_array($chave, $this->attributes)){
                if($item == false || $item == '' || $item == null) continue;
                $this->campos[$chave] = $item;
            }
        }
    }

    public function set($chave, $valor){
        if(!in_array($chave, $this->attributes))
            throw new \Exception('Chave informada para o atributo não existe');

        $this->campos[$chave] = $valor;
    }

    public function getCampos(){
        return $this->campos;
    }
} 