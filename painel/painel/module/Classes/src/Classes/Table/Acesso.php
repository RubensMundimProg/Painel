<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Acesso extends AbstractEstruturaTable{

    public $table = 'acesso';
    public $campos = [
        'ID' => 'Id',
        'USUARIO'=>'Usuario',
        'IP'=>'Ip',
        'NAVEGADOR'=>'Navegador',
        'DATA_ACESSO'=>'DataAcesso'
	];

}