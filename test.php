<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-11
 * Time: 下午8:34
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
go(function () {
    $mysqlConfig = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
    \EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',$mysqlConfig);
    $db = \EasySwoole\MysqliPool\Mysql::defer('mysql');

    $mysqlTable = new \AutomaticGeneration\MysqlTable($db, \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.database'));
    $tableName = $this->tableName;
    $tableComment = $mysqlTable->getComment($tableName);
    $tableColumns = $mysqlTable->getColumnList($tableName);
    $path = 'User';

    $beanConfig = new \AutomaticGeneration\Config\BeanConfig();
    $beanConfig->setBaseNamespace("App\\Model\\".$path);
//    $beanConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $beanConfig->setTablePre('');
    $beanConfig->setTableName('user_list');
    $beanConfig->setTableComment($tableColumns);
    $beanConfig->setTableColumns($tableComment);
    $beanBuilder = new \AutomaticGeneration\BeanBuilder($beanConfig);
    $result = $beanBuilder->generateBean();
    var_dump(\App\Model\User\UserBean::class);

    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseNamespace("App\\Model\\".$path);
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelConfig->setTableName("user_list");
    $modelConfig->setTableComment($tableComment);
    $modelConfig->setTableColumns($tableColumns);
    $modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
    $result = $modelBuilder->generateModel();
    var_dump($result);

    $path='Api\\Admin\\User';
    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
    $controllerConfig->setBaseNamespace("App\\HttpController\\".$path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
    $controllerConfig->setTablePre('');
    $controllerConfig->setTableName('user_list');
    $controllerConfig->setTableComment($tableComment);
    $controllerConfig->setTableColumns($tableColumns);
    $controllerConfig->setExtendClass("App\\HttpController\\".$path."\\Base");
    $controllerConfig->setModelClass($modelBuilder->getClassName());
    $controllerConfig->setBeanClass($beanBuilder->getClassName());
    $controllerConfig->setMysqlPoolClass(EasySwoole\MysqliPool\Mysql::class);
    $controllerConfig->setMysqlPoolName('test');
    $controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
    $result = $controllerBuilder->generateController();
    var_dump($result);
    exit();
});