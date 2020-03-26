<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME'   => "EasySwoole",
    'WEB_HOST'      => 'test.php20.cn:9501',
    'MAIN_SERVER'   => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT'           => 9501,
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,
        'RUN_MODEL'      => SWOOLE_PROCESS,
        'SETTING'        => [
            'worker_num'            => 8,
            'package_max_length'    => 1024 * 1024 * 10,
            'task_worker_num'       => 8,
            'reload_async'          => true,
            'document_root'         => EASYSWOOLE_ROOT . '/Static',
            'enable_static_handler' => true,
        ],
    ],
    'TEMP_DIR'      => null,
    'LOG_DIR'       => null,
    'CONSOLE'       => [
        'ENABLE'         => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST'           => '127.0.0.1',
        'PORT'           => 9500,
        'USER'           => 'root',
        'PASSWORD'       => '123456'
    ],
    'FAST_CACHE'    => [
        'PROCESS_NUM' => 0,
        'BACKLOG'     => 256,
    ],
    'DISPLAY_ERROR' => true,
    'PHAR'          => [
        'EXCLUDE' => ['.idea', 'Log', 'Temp', 'easyswoole', 'easyswoole.install']
    ],
    'MYSQL'         => [
        //数据库配置
        'host'                 => '127.0.0.1',//数据库连接ip
        'user'                 => 'demo',//数据库用户名
        'password'             => '123456',//数据库密码
        'database'             => 'demo',//数据库
        'port'                 => '3306',//端口
        'timeout'              => '30',//超时时间
        'connect_timeout'      => '5',//连接超时时间
        'charset'              => 'utf8',//字符编码
        'strict_type'          => false, //开启严格模式，返回的字段将自动转为数字类型
        'fetch_mode'           => false,//开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
        'alias'                => '',//子查询别名
        'isSubQuery'           => false,//是否为子查询
        'max_reconnect_times ' => '3',//最大重连次数

        //连接池配置
        'intervalCheckTime'    => 30 * 1000,//定时验证对象是否可用以及保持最小连接的间隔时间
        'maxIdleTime'          => 15,//最大存活时间,超出则会每$intervalCheckTime/1000秒被释放
        'maxObjectNum'         => 20,//最大创建数量
        'minObjectNum'         => 5,//最小创建数量 最小创建数量不能大于等于最大创建数量,否则报错 每$intervalCheckTime/1000秒去判断最小连接数
        'getObjectTimeout'     => 3.0,//连接池对象获取连接对象时的等待时间
    ],
];
