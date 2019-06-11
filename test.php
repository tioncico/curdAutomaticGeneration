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
    $tableAutomatic = new \AutomaticGeneration\TableAutomatic('user_list');
    $path = 'User';

    $beanConfig = new \AutomaticGeneration\Config\BeanConfig();
    $beanConfig->setBaseNamespace("App\\Model\\".$path);
//    $beanConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $beanConfig->setTablePre('');
    $beanConfig->setTableName('user_list');
    $beanConfig->setTableComment($tableAutomatic->tableComment);
    $beanConfig->setTableColumns($tableAutomatic->tableColumns);
    $beanBuilder = new \AutomaticGeneration\BeanBuilder($beanConfig);
    $result = $beanBuilder->generateBean();
    var_dump(\App\Model\User\UserBean::class);

    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseNamespace("App\\Model\\".$path);
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelConfig->setTableName("user_list");
    $modelConfig->setTableComment($tableAutomatic->tableComment);
    $modelConfig->setTableColumns($tableAutomatic->tableColumns);
    $modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
    $result = $modelBuilder->generateModel();
    var_dump($result);

    $path='Api\\User\\User';
    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
    $controllerConfig->setBaseNamespace("App\\HttpController\\".$path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
    $controllerConfig->setTablePre($tableAutomatic->tablePre);
    $controllerConfig->setTableName($tableAutomatic->tableName);
    $controllerConfig->setTableComment($tableAutomatic->tableComment);
    $controllerConfig->setTableColumns($tableAutomatic->tableColumns);
    $controllerConfig->setExtendClass(\App\HttpController\Base::class);
//    $controllerConfig->setExtendClass("App\\HttpController\\".$path."\\Base");
    $controllerConfig->setModelClass($modelBuilder->getClassName());
    $controllerConfig->setBeanClass($beanBuilder->getClassName());
    $controllerConfig->setMysqlPoolClass(EasySwoole\MysqliPool\Mysql::class);
    $controllerConfig->setMysqlPoolName('mysql');
    $controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
    $result = $controllerBuilder->generateController();
    var_dump($result);
    exit();
});