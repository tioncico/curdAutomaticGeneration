<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 18:03
 */

namespace AutomaticGeneration;

use App\Utility\Pool\MysqlPoolObject;

class MysqlTable
{
    /**
     * @var $db MysqlPoolObject
     */
    protected $db;
    protected $dbName;

    public function __construct(MysqlPoolObject $db, $dbName)
    {
        $this->db = $db;
        $this->dbName = $dbName;

//        $this->getDb()->rawQuery("use {$dbName};");
    }

    public function getColumnList($tableName)
    {
        $tableColumns = $this->getDb()->rawQuery("show full columns from {$tableName}");

        return $tableColumns;
    }

    public function getComment($tableName)
    {
        $dbName = $this->dbName;
        $tableComment = $this->getDb()->rawQuery("select TABLE_COMMENT from information_schema.TABLES WHERE TABLE_NAME = '{$tableName}' AND TABLE_SCHEMA = '{$dbName}'")[0]['TABLE_COMMENT'];
        return $tableComment;
    }


    /**
     * @return MysqlPoolObject
     */
    public function getDb(): MysqlPoolObject
    {
        return $this->db;
    }


}