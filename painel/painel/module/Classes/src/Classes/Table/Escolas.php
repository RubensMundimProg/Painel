<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class Escolas extends AbstractEstruturaTable{

    public $table = 'escolas';
    public $campos = [
        'ID' => 'Id',
        'NOME' => 'Nome',
        'UF' => 'Uf' ,
        'MUNICIPIO' => 'Municipio' ,
        'LATITUDE' => 'Latitude' ,
        'LONGITUDE' => 'Longitude' ,
        'ENDERECO' => 'Endereco' ,
	];

}