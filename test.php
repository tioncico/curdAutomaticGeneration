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
    $table = [
        'tableName'     => 'admin_list',
        'charset'       => 'utf8',
        'comment'       => '管理员列表',
        'engine'        => \AutomaticGeneration\MysqlTableBean\TableBean::ENGINE_INNODB,
        'columnList'    => [
            [
                'columnName'      => '',
                'columnType'      => '',
                'typeLength'      => '',
                'typeLengthPoint' => '',
                'notNull'         => '',
                'default'         => '',
                'comment'         => '',
                'ai'              => '',
                'unsigned'        => '',
                'zeroFill'        => '',
                'primary'         => '',
            ]
        ],
        'primaryColumn' => '',
        'indexList'     => [

        ],
    ];


    $table = new \AutomaticGeneration\MysqlTableBean\TableBean();
    $table->setTableName('test');


});