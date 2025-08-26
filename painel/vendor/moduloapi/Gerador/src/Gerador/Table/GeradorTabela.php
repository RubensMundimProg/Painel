<?php

namespace Gerador\Table;

use Estrutura\Table\AbstractEstruturaTable;

class GeradorTabela extends AbstractEstruturaTable{

    public $table = 'TABLES';
    public $campos = [
        'TABLE_CATALOG'=>'tableCatalog',
        'TABLE_SCHEMA'=>'tableSchema',
        'TABLE_NAME'=>'tableName',
        'TABLE_TYPE'=>'tableType',
        'ENGINE'=>'engine',
        'VERSION'=>'version',
        'ROW_FORMAT'=>'rowFormat',
        'TABLE_ROWS'=>'tableRows',
        'AVG_ROW_LENGTH'=>'avgRowLength',
        'DATA_LENGTH'=>'dataLength',
        'MAX_DATA_LENGTH'=>'maxDataLength',
        'INDEX_LENGTH'=>'indexLength',
        'DATA_FREE'=>'dataFree',
        'AUTO_INCREMENT'=>'autoIncrement',
        'CREATE_TIME'=>'createTime',
        'UPDATE_TIME'=>'updateTime',
        'CHECK_TIME'=>'checkTime',
        'TABLE_COLLATION'=>'tableCollation',
        'CHECKSUM'=>'checksum',
        'CREATE_OPTIONS'=>'createOptions',
        'TABLE_COMMENT'=>'tableComment'
    ];

}