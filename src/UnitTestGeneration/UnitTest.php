<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-26
 * Time: 下午8:45
 */

namespace AutomaticGeneration\UnitTestGeneration;


use AutomaticGeneration\Config\UnitTestConfig;
use AutomaticGeneration\GenerationBase;
use AutomaticGeneration\Unity;
use EasySwoole\ORM\Utility\Schema\Column;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\Utility\Random;

class UnitTest extends GenerationBase
{
    /**
     * @var UnitTestConfig
     */
    protected $config;

    protected $addActionName = 'add';
    protected $updateActionName = 'update';
    protected $delActionName = 'del';
    protected $getOneActionName = 'getOne';
    protected $getListActionName = 'getList';

    public function __construct(UnitTestConfig $config)
    {
        parent::__construct($config);
    }

    function getClassName()
    {
        $className = $this->config->getRealTableName() . 'Test';
        return $className;
    }

    function addClassData()
    {
        $this->phpNamespace->addUse($this->config->getModelClass());
        $this->addProperty();
        $this->addTestAddMethod();
        $this->addTestUpdateMethod();
        $this->addTestDelMethod();
        $this->addTestGetOneMethod();
        $this->addTestGetListMethod();

    }

    protected function addProperty()
    {
        $this->phpClass->addProperty('modelName', $this->getApiUrl());
    }

    protected function addTestAddMethod()
    {
        $method = $this->phpClass->addMethod('testAdd');
        $body = <<<BODY
\$data = [];

BODY;
        $body .= $this->getTableTestData('data');
        $modelName = Unity::getModelName($this->config->getModelClass());

        $body .= <<<BODY
\$response = \$this->request('{$this->addActionName}',\$data);
\$model = new {$modelName}();
\$model->destroy(\$response->result->{$this->config->getTable()->getPkFiledName()});
//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));
BODY;
        $method->setBody($body);
    }

    protected function addTestUpdateMethod()
    {
        $method = $this->phpClass->addMethod('testUpdate');
        $body = <<<BODY
\$data = [];

BODY;
        $body .= $this->getTableTestData('data');

        $modelName = Unity::getModelName($this->config->getModelClass());
        $body .= <<<BODY
\$model = new {$modelName}();
\$model->data(\$data)->save();    

\$update = [];
\$update['{$this->config->getTable()->getPkFiledName()}'] = \$model->{$this->config->getTable()->getPkFiledName()};

BODY;

        $body .= $this->getTableTestData('update');

        $body .= <<<BODY
\$response = \$this->request('{$this->updateActionName}',\$update);
\$model->destroy(\$model->{$this->config->getTable()->getPkFiledName()});
//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));
BODY;
        $method->setBody($body);
    }

    protected function addTestDelMethod()
    {
        $method = $this->phpClass->addMethod('testDel');
        $body = <<<BODY
\$data = [];

BODY;
        $body .= $this->getTableTestData('data');

        $modelName = Unity::getModelName($this->config->getModelClass());
        $body .= <<<BODY
\$model = new {$modelName}();
\$model->data(\$data)->save();    

\$delData = [];
\$delData['{$this->config->getTable()->getPkFiledName()}'] = \$model->{$this->config->getTable()->getPkFiledName()};
\$response = \$this->request('{$this->delActionName}',\$delData);
//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));

BODY;
        $method->setBody($body);
    }

    protected function addTestGetOneMethod(){

        $method = $this->phpClass->addMethod('testGetOne');
        $body = <<<BODY
\$data = [];

BODY;
        $body .= $this->getTableTestData('data');

        $modelName = Unity::getModelName($this->config->getModelClass());
        $body .= <<<BODY
\$model = new {$modelName}();
\$model->data(\$data)->save();    

\$data = [];
\$data['{$this->config->getTable()->getPkFiledName()}'] = \$model->{$this->config->getTable()->getPkFiledName()};
\$response = \$this->request('{$this->getOneActionName}',\$data);
\$model->destroy(\$model->{$this->config->getTable()->getPkFiledName()});

//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));

BODY;
        $method->setBody($body);
    }

    protected function addTestGetListMethod(){

        $method = $this->phpClass->addMethod('testGetList');
        $modelName = Unity::getModelName($this->config->getModelClass());
        $body = <<<BODY
\$model = new {$modelName}();
\$data = [];
\$response = \$this->request('{$this->getListActionName}',\$data);

//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));

BODY;
        $method->setBody($body);
    }

    protected function randColumnTypeValue(Column $column)
    {
        $columnType = Unity::convertDbTypeToDocType($column->getColumnType());
        $value = null;
        switch ($columnType) {
            case "int":
                if ($column->getColumnLimit() <= 3) {
                    $value = mt_rand(0, 3);
                } else {
                    $value = mt_rand(10000, 99999);
                }
                break;
            case "float":
                if ($column->getColumnLimit() <= 3) {
                    $value = mt_rand(10, 30) / 10;
                } else {
                    $value = mt_rand(100000, 999999) / 10;
                }
                break;
            case "string":
                $value = '测试文本' . Random::character(6);
                break;
            case "mixed":
                $value = null;
                break;
        }
        return $value;
    }

    protected function getApiUrl()
    {
        $baseNamespace = $this->config->getControllerClass();
        $modelName = str_replace(['App\\HttpController', '\\'], ['', '/'], $baseNamespace);
        return $modelName;
    }

    protected function getTableTestData($variableName = 'data')
    {
        $data = '';

        Unity::chunkTableColumn($this->config->getTable(), function (Column $column, string $columnName) use (&$data, $variableName) {
            if ($columnName == $this->config->getTable()->getPkFiledName()) {
                return false;
            }
            $value = $this->randColumnTypeValue($column);
            $data .= "\${$variableName}['{$columnName}'] = '{$value}';\n";
        });
        return $data;
    }

}