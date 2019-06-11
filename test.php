<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-11
 * Time: 下午8:34
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
go(function (){
    $mysqlConfig = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
    $mysqlPoolConfig = \EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',$mysqlConfig);
//根据返回的poolConfig对象进行配置连接池配置项
    $mysqlPoolConfig->setMaxObjectNum(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.maxObjectNum'));

    $mysqlTableData = new \AutomaticGeneration\MysqlTable(\EasySwoole\MysqliPool\Mysql::defer('mysql    '),'test');
    var_dump($mysqlTableData->getColumnList('test'));
});