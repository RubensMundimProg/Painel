<?php

namespace Gerador\Entity;

use Estrutura\Service\AbstractEstruturaService;

class GeradorTabela extends AbstractEstruturaService{
    protected $tableCatalog;
    protected $tableSchema;
    protected $tableName;
    protected $tableType;
    protected $engine;
    protected $version;
    protected $rowFormat;
    protected $avgRowLength;
    protected $dataLength;
    protected $maxDataLength;
    protected $indexLength;
    protected $dataFree;
    protected $autoIncrement;
    protected $createTime;
    protected $updateTime;
    protected $checkTime;
    protected $tableCollation;
    protected $checksum;
    protected $createOptions;
    protected $tableCommen;
    protected $tableRows;
    protected $tableComment;

    /**
     * @param mixed $tableComment
     */
    public function setTableComment($tableComment)
    {
        $this->tableComment = $tableComment;
    }

    /**
     * @return mixed
     */
    public function getTableComment()
    {
        return $this->tableComment;
    }

    /**
     * @param mixed $tableRows
     */
    public function setTableRows($tableRows)
    {
        $this->tableRows = $tableRows;
    }

    /**
     * @return mixed
     */
    public function getTableRows()
    {
        return $this->tableRows;
    }

    /**
     * @param mixed $autoIncrement
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * @return mixed
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param mixed $avgRowLength
     */
    public function setAvgRowLength($avgRowLength)
    {
        $this->avgRowLength = $avgRowLength;
    }

    /**
     * @return mixed
     */
    public function getAvgRowLength()
    {
        return $this->avgRowLength;
    }

    /**
     * @param mixed $checkTime
     */
    public function setCheckTime($checkTime)
    {
        $this->checkTime = $checkTime;
    }

    /**
     * @return mixed
     */
    public function getCheckTime()
    {
        return $this->checkTime;
    }

    /**
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * @param mixed $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param mixed $dataFree
     */
    public function setDataFree($dataFree)
    {
        $this->dataFree = $dataFree;
    }

    /**
     * @return mixed
     */
    public function getDataFree()
    {
        return $this->dataFree;
    }

    /**
     * @param mixed $dataLength
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = $dataLength;
    }

    /**
     * @return mixed
     */
    public function getDataLength()
    {
        return $this->dataLength;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param mixed $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @return mixed
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * @param mixed $indexLength
     */
    public function setIndexLength($indexLength)
    {
        $this->indexLength = $indexLength;
    }

    /**
     * @return mixed
     */
    public function getIndexLength()
    {
        return $this->indexLength;
    }

    /**
     * @param mixed $maxDataLength
     */
    public function setMaxDataLength($maxDataLength)
    {
        $this->maxDataLength = $maxDataLength;
    }

    /**
     * @return mixed
     */
    public function getMaxDataLength()
    {
        return $this->maxDataLength;
    }

    /**
     * @param mixed $rowFormat
     */
    public function setRowFormat($rowFormat)
    {
        $this->rowFormat = $rowFormat;
    }

    /**
     * @return mixed
     */
    public function getRowFormat()
    {
        return $this->rowFormat;
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
     * @param mixed $tableCollation
     */
    public function setTableCollation($tableCollation)
    {
        $this->tableCollation = $tableCollation;
    }

    /**
     * @return mixed
     */
    public function getTableCollation()
    {
        return $this->tableCollation;
    }

    /**
     * @param mixed $tableCommen
     */
    public function setTableCommen($tableCommen)
    {
        $this->tableCommen = $tableCommen;
    }

    /**
     * @return mixed
     */
    public function getTableCommen()
    {
        return $this->tableCommen;
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

    /**
     * @param mixed $tableType
     */
    public function setTableType($tableType)
    {
        $this->tableType = $tableType;
    }

    /**
     * @return mixed
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $createOptions
     */
    public function setCreateOptions($createOptions)
    {
        $this->createOptions = $createOptions;
    }

    /**
     * @return mixed
     */
    public function getCreateOptions()
    {
        return $this->createOptions;
    }


} 