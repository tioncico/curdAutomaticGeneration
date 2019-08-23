<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-12
 * Time: 下午8:38
 */

namespace AutomaticGeneration\MysqlTableBean;


use EasySwoole\Spl\SplBean;

class ColumnIndexBean extends SplBean
{
    protected $columnList = [];
    protected $indexName;
    protected $indexType;
    protected $indexMethod;

    const INDEX_TYPE_NORMAL = 'NORMAL';
    const INDEX_TYPE_UNIQUE = 'UNIQUE';
    const INDEX_TYPE_FULLTEXT = 'FULLTEXT';

    public function addColumn($columnName)
    {
        $this->columnList[] = $columnName;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumnList(): array
    {
        return $this->columnList;
    }

    /**
     * @param array $columnList
     */
    public function setColumnList(array $columnList): void
    {
        $this->columnList = $columnList;
    }

    /**
     * @return mixed
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param mixed $indexName
     */
    public function setIndexName($indexName): void
    {
        $this->indexName = $indexName;
    }

    /**
     * @return mixed
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @param mixed $indexType
     */
    public function setIndexType($indexType): void
    {
        $this->indexType = $indexType;
    }

    /**
     * @return mixed
     */
    public function getIndexMethod()
    {
        return $this->indexMethod;
    }

    /**
     * @param mixed $indexMethod
     */
    public function setIndexMethod($indexMethod): void
    {
        $this->indexMethod = $indexMethod;
    }

}