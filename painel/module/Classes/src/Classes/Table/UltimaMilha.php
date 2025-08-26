<?php

namespace Classes\Table;

use Estrutura\Table\AbstractEstruturaTable;

class UltimaMilha extends AbstractEstruturaTable{

    public $table = 'ultima_milha';
    public $campos = [
        'ID' => 'Id',
        'UF' => 'uF',
        'NOME_ESCOLA' => 'NomeEscola' ,
        'STATUS' => 'Status' ,
        'DATA_HORA'=> 'DataHora' ,
        'LATITUDE' => 'Latitude' ,
        'LONGITUDE' => 'Longitude' ,
        'ID_ESCOLA' => 'IdEscola' ,
        'MUNICIPIO' => 'Municipio'
    ];
}