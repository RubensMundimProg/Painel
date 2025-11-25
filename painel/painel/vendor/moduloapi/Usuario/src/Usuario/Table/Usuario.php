<?php

namespace Usuario\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Usuario extends AbstractEstruturaTable{

    public $table = 'USUARIO_USUARIO';
    public $campos = [
        'ID_USUARIO'=>'id',
        'NOME'=>'nome',
        'EMAIL'=>'email',
        'SENHA'=>'senha',
        'STATUS'=>'status'
    ];

}