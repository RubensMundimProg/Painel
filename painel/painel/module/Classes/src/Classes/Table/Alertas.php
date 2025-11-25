<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Alertas extends AbstractEstruturaTable{

    public $table = 'eventos';
    public $campos = [
        'ID' => 'id',
        'Titulo' => 'Titulo',
        'Municipio' => 'Municipio' ,
        'Uf' => 'Uf' ,
        'Descricao' => 'Descricao' ,
        'Descricao_Risco' => 'DescricaoRisco' ,
        'Nivel_Alerta' => 'NivelAlerta' ,
        'Categoria' => 'Categoria' ,
        'SubCategoria' => 'SubCategoria' ,
        'Ocorrencia' => 'Ocorrencia' ,
        'Coordenacao' => 'Coordenacao' ,
        'ImpactoAplicacao' => 'ImpactoAplicacao' ,
        'NroProcesso' => 'NroProcesso' ,
        'Anexo' => 'Anexo' ,
        'Usuario' => 'Usuario' ,
        'UsuarioTriagem' => 'UsuarioTriagem' ,
        'DataHora' => 'DataHora' ,
        'Status' => 'Status',
        'OrigemInformacao' => 'OrigemInformacao',
        'ProvidenciasAdotadas' => 'ProvidenciasAdotadas',
        'InformacoesAdicionais' => 'InformacoesAdicionais',
        'CodigoOrigem'=>'CodigoOrigem',
        'CodigoRm'=>'CodigoRm',
        'Sistema'=>'Sistema',
        'DiaAplicacao'=>'DiaAplicacao',
        'Ano'=>'Ano'
	];

}