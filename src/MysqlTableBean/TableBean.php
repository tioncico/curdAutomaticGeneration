<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-12
 * Time: 下午8:13
 */

namespace AutomaticGeneration\MysqlTableBean;


use EasySwoole\Spl\SplBean;

class TableBean extends SplBean
{
    const ENGINE_INNODB = 'INNODB';
    const ENGINE_CSV = 'CSV';
    const ENGINE_MEMORY = 'MEMORY';
    const ENGINE_MYISAM = 'MYISAM';

    protected $charset;
    protected $tableName;
    protected $comment;
    protected $engine;
    protected $columnList = [];
    protected $indexList = [];
    protected $primaryColumn;

    /**
     * @return mixed
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param mixed $charset
     */
    public function setCharset($charset): void
    {
        $this->charset = $charset;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName): void
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine): void
    {
        $this->engine = $engine;
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
     * @return array
     */
    public function getIndexList(): array
    {
        return $this->indexList;
    }

    /**
     * @param array $indexList
     */
    public function setIndexList(array $indexList): void
    {
        $this->indexList = $indexList;
    }

    /**
     * @return mixed
     */
    public function getPrimaryColumn()
    {
        return $this->primaryColumn;
    }

    /**
     * @param mixed $primaryColumn
     */
    public function setPrimaryColumn($primaryColumn): void
    {
        $this->primaryColumn = $primaryColumn;
    }


}