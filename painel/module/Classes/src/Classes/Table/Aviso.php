<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Aviso extends AbstractEstruturaTable{

    public $table = 'aviso';
    public $campos = [
        'ID' => 'Id',
        'AVALIACAO_PEDAGOGICA'=>'AvaliacaoPedagogica',
        'TITULO'=>'Titulo',
        'ANEXO'=>'Anexo',
        'TEXTO'=>'Texto',
        'DATA_INICIO'=>'DataInicio',
        'HORA_INICIO'=>'HoraInicio',
        'DATA_FIM'=>'DataFim',
        'HORA_FIM'=>'HoraFim'
	];

}