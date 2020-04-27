<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-27
 * Time: 下午8:55
 */

namespace AutomaticGeneration\InitGeneration;


use AutomaticGeneration\Config\BaseConfig;
use AutomaticGeneration\GenerationBase;
use Curl\Curl;
use PHPUnit\Framework\TestCase;

class BaseUnitTest extends GenerationBase
{
    protected $apiBase='http://127.0.0.1:9501';
    public function __construct(?BaseConfig $config = null)
    {
        if (empty($config)) {
            $config = new BaseConfig();
            $config->setExtendClass(TestCase::class);
            $config->setBaseNamespace("UnitTest");
        }
        parent::__construct($config);
    }
    function getClassName()
    {
        return "BaseTest";
    }

    function addClassData()
    {
        $this->phpNamespace->addUse(\EasySwoole\EasySwoole\Core::class);
        $this->phpNamespace->addUse(TestCase::class);
        $this->phpNamespace->addUse(Curl::class);
        $this->addProperty();
        $this->addRequest();
        $this->addSetUp();
    }

    protected function addProperty()
    {
        $class = $this->phpClass;
        $class->addProperty('isInit', 0)->setStatic();
        $class->addProperty('curl')->setComment("@var Curl");
        $class->addProperty('apiBase',$this->apiBase);
        $class->addProperty('modelName');
    }

    protected function addSetUp()
    {
        $this->phpClass->addMethod('setUp')->setBody(<<<BODY
if (self::\$isInit == 1) {
    return true;
}
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', dirname(__FILE__, 2));
require_once dirname(__FILE__, 2) . '/EasySwooleEvent.php';
Core::getInstance()->initialize()->globalInitialize();
self::\$isInit = 1;
\$this->curl = new Curl();
BODY
        );
    }

    protected function addRequest()
    {
        $method = $this->phpClass->addMethod('request');
        $method->addParameter('action');
        $method->addParameter('data')->setDefaultValue([]);
        $method->addParameter('modelName')->setDefaultValue(null);
        $method->setBody(<<<BODY
\$modelName = \$modelName ?? \$this->modelName;
\$url = \$this->apiBase . '/' . \$modelName . '/' . \$action;
\$curl = \$this->curl;
\$curl->post(\$url, \$data);
if (\$curl->response) {
//            var_dump(\$curl->response);
} else {
    echo 'Error: ' . \$curl->errorCode . ': ' . \$curl->errorMessage . "\n";
}
\$this->assertTrue(!!\$curl->response);
\$this->assertEquals(200, \$curl->response->code, \$curl->response->msg);
return \$curl->response;
BODY
        );
    }


}