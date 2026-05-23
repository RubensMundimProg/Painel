<?php

namespace Gerador\Table;

use Estrutura\Table\AbstractEstruturaTable;

class GeradorColuna extends AbstractEstruturaTable{

    public $table = 'COLUMNS';
    public $campos = [
        'TABLE_CATALOG'=>'tableCatalog',
        'TABLE_SCHEMA'=>'tableSchema',
        'TABLE_NAME'=>'tableName',
        'COLUMN_NAME'=>'columnName',
        'ORDINAL_POSITION'=>'ordinalPosition',
        'COLUMN_DEFAULT'=>'columnDefault',
        'IS_NULLABLE'=>'isNullable',
        'DATA_TYPE'=>'dataType',
        'CHARACTER_MAXIMUM_LENGTH'=>'characterMaximumLength',
        'CHACACTER_OCTET_LENGTH'=>'chacacterOctetLength',
        'NUMERIC_PRECISION'=>'numericPrecision',
        'NUMERIC_SCALE'=>'numericScale',
        'CHARACTER_SET_NAME'=>'characterSetName',
        'COLLATION_NAME'=>'collationName',
        'COLUMN_TYPE'=>'columnType',
        'COLUMN_KEY'=>'columnKey',
        'EXTRA'=>'extra',
        'PRIVILAGES'=>'privilages',
        'COLUMN_COMMENT'=>'columnComment'
    ];

}