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

    function initTableInfo()
    {
        $db = \App\Utility\Pool\MysqlPool::defer();
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

    function action($generateBean = true, $generateModel = true, $generateController = true)
    {

        if ($generateBean) {
            $result = $this->generateBean($this->tableColumns, $this->tableComment);
            if ($result) {
                echo "生成[{$result}]成功\n";
            } else {
                echo "生成bean失败";
            }
        }
        if ($generateModel) {
            $result = $this->generateModel($this->tableColumns, $this->tableComment, BaseModel::class);
            if ($result) {
                echo "生成[{$result}]成功\n";
            } else {
                echo "生成Model失败";
            }
        }
        if ($generateController) {
            $result = $this->generateController($this->tableColumns, $this->tableComment, ApiBase::class);
            if ($result) {
                echo "生成[{$result}]成功\n";
            } else {
                echo "生成Controller失败";
            }
        }

        exit;
    }

    function generateModel($tableColumns, $tableComment, $extendClass = null)
    {
        $modelConfig = new ModelConfig();
        $modelConfig->setBaseDirectory($this->baseDir);
        $modelConfig->setBaseNamespace($this->modelNamespace);
        $modelConfig->setTablePre($this->tablePre);
        $modelConfig->setExtendClass($extendClass ?? BaseModel::class);
        $modelConfig->setTableName($this->tableName);
        $modelConfig->setTableComment($tableComment);
        $modelConfig->setTableColumns($tableColumns);
        $modelBuilder = new ModelBuilder($modelConfig);
        return $modelBuilder->generateModel();
    }

    function generateBean($tableColumns, $tableComment)
    {
        $beanConfig = new BeanConfig();
        $beanConfig->setBaseDirectory($this->baseDir);
        $beanConfig->setBaseNamespace($this->beanNamespace);
        $beanConfig->setTablePre($this->tablePre);
        $beanConfig->setTableName($this->tableName);
        $beanConfig->setTableComment($tableComment);
        $beanConfig->setTableColumns($tableColumns);
        $beanBuilder = new BeanBuilder($beanConfig);
        return $beanBuilder->generateBean();
    }


    function generateController($tableColumns, $tableComment, $extendClass)
    {
        $controllerConfig = new ControllerConfig();
        $controllerConfig->setBaseDirectory($this->baseDir);
        $controllerConfig->setBaseNamespace($this->controllerNamespace);
        $controllerConfig->setTablePre($this->tablePre);
        $controllerConfig->setTableName($this->tableName);
        $controllerConfig->setTableComment($tableComment);
        $controllerConfig->setTableColumns($tableColumns);
        $controllerConfig->setExtendClass($extendClass ?? ApiBase::class);
        $controllerConfig->setModelClass(UserModel::class);
        $controllerConfig->setBeanClass(UserBean::class);
        $controllerConfig->setMysqlPoolClass(MysqlPool::class);
        $controllerBuilder = new ControllerBuilder($controllerConfig);
        return $controllerBuilder->generateController();
    }

}
