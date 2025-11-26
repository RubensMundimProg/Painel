<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Municipios extends AbstractEstruturaTable{

    public $table = 'municipios';
    public $campos = [
        'ID' => 'Id',
        'PATCH' => 'Patch',
        'NOME' => 'Nome' ,
	];

}