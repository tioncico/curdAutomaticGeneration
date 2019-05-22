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
use App\Model\BaseModel;
use App\Model\UserBean;
use App\Model\UserModel;
use App\Utility\Pool\MysqlPool;
use AutomaticGeneration\Config\BeanConfig;
use AutomaticGeneration\Config\ControllerConfig;
use AutomaticGeneration\Config\ModelConfig;
use EasySwoole\MysqliPool\Mysql;

class TableAutomatic
{
    const APP_PATH = 'Application';
    public $tableName;
    public $tablePre;
    public $tableColumns;
    public $tableComment;

    public function __construct($tableName, $tablePre)
    {
        $this->tableName = $tableName;
        $this->tablePre = $tablePre;
        $this->initTableInfo();
    }

    function initTableInfo($db)
    {
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
