<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2019-03-26
 * Time: 19:12
 */
namespace AutomaticGeneration\Test;
use EasySwoole\EasySwoole\Core;
use PHPUnit\Framework\TestCase;

/**
 * 基础测试基类
 * Class BaseTestCase
 * @package Test
 */
class BaseTestCase extends TestCase
{
    /**
     * 准备测试基境
     * @return void
     */
    function setUp(): void
    {
        require_once dirname(__FILE__,2) . '/vendor/autoload.php'; // 引入composer包
        defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT',dirname(__FILE__,2));
        require_once dirname(__FILE__,2).'/EasySwooleEvent.php';
        Core::getInstance()->initialize();
        parent::setUp();
    }

    /**
     * 测试环境清理
     * @return void
     */
    function tearDown(): void
    {
//        var_dump('call tearDown');
        parent::tearDown();
    }
}