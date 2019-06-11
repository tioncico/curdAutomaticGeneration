<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 22:28
 */

namespace AutomaticGeneration;

use App\HttpController\Api\ApiBase;
use App\HttpController\Base;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\MysqliPool\Mysql;

class TableAutomatic
{
    const APP_PATH = 'Application';
    public $tableName;
    public $tablePre;
    public $tableColumns;
    public $tableComment;

    public function __construct(string $tableName, string $tablePre='')
    {
        $this->tableName = $tableName;
        $this->tablePre = $tablePre;
        $this->initTableInfo();
    }

    function initTableInfo()
    {
        $mysqlConfig = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        Mysql::getInstance()->register('mysql',$mysqlConfig);
        $db = Mysql::defer('mysql');

        $mysqlTable = new MysqlTable($db, \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.database'));
        $tableName = $this->tableName;
        $tableColumns = $mysqlTable->getColumnList($tableName);
        $tableComment = $mysqlTable->getComment($tableName);
        if (empty($tableColumns)) {
            throw new \Exception("{$tableName}表不存在");
        }
        $this->tableColumns = $tableColumns;
        $this->tableComment = $tableComment;
    }

}
