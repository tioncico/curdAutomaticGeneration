<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 18:03
 */

namespace AutomaticGeneration;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\Connection;

class MysqlTable
{
    /**
     * @var $db Connection
     */
    protected $db;
    protected $dbName;

    public function __construct(Connection $db, $dbName)
    {
        $this->db = $db;
        $this->dbName = $dbName;

    }

    public function getColumnList($tableName)
    {
        $query = new QueryBuilder();
        $query->raw("show full columns from {$tableName}");

        $tableColumns = $this->db->query($query)->getResult();

        return $tableColumns;
    }

    public function getComment($tableName)
    {
        $dbName = $this->dbName;
        $query = new QueryBuilder();
        $query->raw("select TABLE_COMMENT from information_schema.TABLES WHERE TABLE_NAME = '{$tableName}' AND TABLE_SCHEMA = '{$dbName}'");

        $tableComment = $this->db->query($query)->getResult()[0]['TABLE_COMMENT'];
        return $tableComment;
    }

}