<?php

namespace Gerador\Entity;

use Estrutura\Service\AbstractEstruturaService;

class GeradorColuna extends AbstractEstruturaService{

    protected $tableCatalog;
    protected $tableSchema;
    protected $tableName;
    protected $columnName;
    protected $ordinalPosition;
    protected $columnDefault;
    protected $isNullable;
    protected $dataType;
    protected $characterMaximumLength;
    protected $chacacterOctetLength;
    protected $numericPrecision;
    protected $numericScale;
    protected $characterSetName;
    protected $collationName;
    protected $columnType;
    protected $columnKey;
    protected $extra;
    protected $privilages;
    protected $columnComment;

    /**
     * @param mixed $chacacterOctetLength
     */
    public function setChacacterOctetLength($chacacterOctetLength)
    {
        $this->chacacterOctetLength = $chacacterOctetLength;
    }

    /**
     * @return mixed
     */
    public function getChacacterOctetLength()
    {
        return $this->chacacterOctetLength;
    }

    /**
     * @param mixed $characterMaximumLength
     */
    public function setCharacterMaximumLength($characterMaximumLength)
    {
        $this->characterMaximumLength = $characterMaximumLength;
    }

    /**
     * @return mixed
     */
    public function getCharacterMaximumLength()
    {
        return $this->characterMaximumLength;
    }

    /**
     * @param mixed $characterSetName
     */
    public function setCharacterSetName($characterSetName)
    {
        $this->characterSetName = $characterSetName;
    }

    /**
     * @return mixed
     */
    public function getCharacterSetName()
    {
        return $this->characterSetName;
    }

    /**
     * @param mixed $collationName
     */
    public function setCollationName($collationName)
    {
        $this->collationName = $collationName;
    }

    /**
     * @return mixed
     */
    public function getCollationName()
    {
        return $this->collationName;
    }

    /**
     * @param mixed $columnComment
     */
    public function setColumnComment($columnComment)
    {
        $this->columnComment = $columnComment;
    }

    /**
     * @return mixed
     */
    public function getColumnComment()
    {
        return $this->columnComment;
    }

    /**
     * @param mixed $columnDefault
     */
    public function setColumnDefault($columnDefault)
    {
        $this->columnDefault = $columnDefault;
    }

    /**
     * @return mixed
     */
    public function getColumnDefault()
    {
        return $this->columnDefault;
    }

    /**
     * @param mixed $columnKey
     */
    public function setColumnKey($columnKey)
    {
        $this->columnKey = $columnKey;
    }

    /**
     * @return mixed
     */
    public function getColumnKey()
    {
        return $this->columnKey;
    }

    /**
     * @param mixed $columnName
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @param mixed $columnType
     */
    public function setColumnType($columnType)
    {
        $this->columnType = $columnType;
    }

    /**
     * @return mixed
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * @param mixed $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $isNullable
     */
    public function setIsNullable($isNullable)
    {
        $this->isNullable = $isNullable;
    }

    /**
     * @return mixed
     */
    public function getIsNullable()
    {
        return $this->isNullable;
    }

    /**
     * @param mixed $numericPrecision
     */
    public function setNumericPrecision($numericPrecision)
    {
        $this->numericPrecision = $numericPrecision;
    }

    /**
     * @return mixed
     */
    public function getNumericPrecision()
    {
        return $this->numericPrecision;
    }

    /**
     * @param mixed $numericScale
     */
    public function setNumericScale($numericScale)
    {
        $this->numericScale = $numericScale;
    }

    /**
     * @return mixed
     */
    public function getNumericScale()
    {
        return $this->numericScale;
    }

    /**
     * @param mixed $ordinalPosition
     */
    public function setOrdinalPosition($ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;
    }

    /**
     * @return mixed
     */
    public function getOrdinalPosition()
    {
        return $this->ordinalPosition;
    }

    /**
     * @param mixed $privilages
     */
    public function setPrivilages($privilages)
    {
        $this->privilages = $privilages;
    }

    /**
     * @return mixed
     */
    public function getPrivilages()
    {
        return $this->privilages;
    }

    /**
     * @param mixed $tableCatalog
     */
    public function setTableCatalog($tableCatalog)
    {
        $this->tableCatalog = $tableCatalog;
    }

    /**
     * @return mixed
     */
    public function getTableCatalog()
    {
        return $this->tableCatalog;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableSchema
     */
    public function setTableSchema($tableSchema)
    {
        $this->tableSchema = $tableSchema;
    }

    /**
     * @return mixed
     */
    public function getTableSchema()
    {
        return $this->tableSchema;
    }





} 