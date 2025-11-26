<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Atividade extends AbstractEstruturaTable{

    public $table = 'atividade';
    public $campos = [
        'ID' => 'Id',
        'SISTEMA'=>'Sistema',
        'DESCRICAO'=>'Descricao',
        'DATA'=>'Data',
        'HORA_INICIO'=>'HoraInicio',
        'HORA_FIM'=>'HoraFim',
	];

}