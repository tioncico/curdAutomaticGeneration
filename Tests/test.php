<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 12:07
 */
include "../vendor/autoload.php";
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', dirname(__FILE__, 2));
require_once EASYSWOOLE_ROOT . '/EasySwooleEvent.php';
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
$mysqlConfig = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));

\EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',$mysqlConfig);

$init = new \AutomaticGeneration\Init();
//$init->initBaseModel();
//$init->initBaseController();


go(function ()  {
    $db = \EasySwoole\MysqliPool\Mysql::defer('mysql');
    $mysqlTable = new \AutomaticGeneration\MysqlTable($db, \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.database'));
    $tableName = 'user_list';
    $tableColumns = $mysqlTable->getColumnList($tableName);
    $tableComment = $mysqlTable->getComment($tableName);

    $path = '\\User';
    $beanConfig = new \AutomaticGeneration\Config\BeanConfig();
    $beanConfig->setBaseNamespace("App\\Model".$path);
//    $beanConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $beanConfig->setTablePre('');
    $beanConfig->setTableName('user_list');
    $beanConfig->setTableComment($tableComment);
    $beanConfig->setTableColumns($tableColumns);
    $beanBuilder = new \AutomaticGeneration\BeanBuilder($beanConfig);
//    $result = $beanBuilder->generateBean();
//    var_dump($result);
    $path = '\\User';
    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseNamespace("App\\Model".$path);
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelConfig->setTableName("user_list");
    $modelConfig->setTableComment($tableComment);
    $modelConfig->setTableColumns($tableColumns);
    $modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
//    $result = $modelBuilder->generateModel();
//    var_dump($result);
    $path='\\Api\\Admin\\User';
    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
    $controllerConfig->setBaseNamespace("App\\HttpController".$path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
    $controllerConfig->setTablePre('');
    $controllerConfig->setTableName('user_list');
    $controllerConfig->setTableComment($tableComment);
    $controllerConfig->setTableColumns($tableColumns);
    $controllerConfig->setExtendClass(\App\HttpController\Base::class);
    $controllerConfig->setModelClass($modelBuilder->getClassName());
    $controllerConfig->setBeanClass($beanBuilder->getClassName());
    $controllerConfig->setMysqlPoolClass(EasySwoole\MysqliPool\Mysql::class);
    $controllerConfig->setMysqlPoolName('mysql');
    $controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
    $result = $controllerBuilder->generateController();
    exit();
});
