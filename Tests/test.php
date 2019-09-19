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
\EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL')));



//
//go(function ()  {
//    $automatic = new \AutomaticGeneration\TableAutomatic('payment_list', '');
//    $beanConfig = new \AutomaticGeneration\Config\BeanConfig();
//    $beanConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/Model/Payment');
//    $beanConfig->setBaseNamespace("App\\Model\\Payment");
//    $beanConfig->setTablePre('');
//    $beanConfig->setTableName('payment_list');
//    $beanConfig->setTableComment($automatic->tableComment);
//    $beanConfig->setTableColumns($automatic->tableColumns);
//    $beanBuilder = new \AutomaticGeneration\BeanBuilder($beanConfig);
//    $result = $beanBuilder->generateBean();
//    var_dump($result);
////    exit();
//});
//
go(function ()  {
    $automatic = new \AutomaticGeneration\TableAutomatic('user_list', '');

    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/Model/');
    $modelConfig->setBaseNamespace("App\\Model");
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelConfig->setTableName("payment_list");
    $modelConfig->setKeyword('test');
    $modelConfig->setTableComment($automatic->tableComment);
    $modelConfig->setTableColumns($automatic->tableColumns);
    $modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
    $result = $modelBuilder->generateModel();
    var_dump($result);
//    exit();
});

//go(function ()  {
//    $automatic = new \AutomaticGeneration\TableAutomatic('goods_recommend_list', '');
//    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/Common/');
//    $controllerConfig->setBaseNamespace("App\\HttpController\\Api\\Common");
//    $controllerConfig->setTablePre($automatic->tablePre);
//    $controllerConfig->setTableName($automatic->tableName);
//    $controllerConfig->setTableComment($automatic->tableComment);
//    $controllerConfig->setTableColumns($automatic->tableColumns);
//    $controllerConfig->setExtendClass(\App\HttpController\Api\Common\CommonBase::class);
//    $controllerConfig->setModelClass(\App\Model\GoodsRecommend\GoodsRecommendModel::class);
//    $controllerConfig->setBeanClass(\App\Model\GoodsRecommend\GoodsRecommendBean::class);
//    $controllerConfig->setMysqlPoolClass(\App\Utility\Pool\MysqlPool::class);
//    $controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
//    $controllerBuilder->generateController();
//    exit();
//});