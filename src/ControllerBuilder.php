<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-5-2
 * Time: 上午10:38
 */

namespace AutomaticGeneration;

use AutomaticGeneration\Config\ControllerConfig;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Utility\File;
use EasySwoole\Utility\Str;
use EasySwoole\Validate\Validate;
use http\Message\Body;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * easyswoole 控制器快速构建器
 * Class ControllerBuilder
 * @package AutomaticGeneration
 */
class ControllerBuilder
{
    /**
     * @var $config BeanConfig;
     */
    protected $config;
    protected $validateList = [];

    /**
     * BeanBuilder constructor.
     * @param        $config
     * @throws \Exception
     */
    public function __construct(ControllerConfig $config)
    {
        $this->config = $config;
        $this->createBaseDirectory($config->getBaseDirectory());
    }

    /**
     * createBaseDirectory
     * @param $baseDirectory
     * @throws \Exception
     * @author Tioncico
     * Time: 19:49
     */
    protected function createBaseDirectory($baseDirectory)
    {
        File::createDirectory($baseDirectory);
    }

    /**
     * generateBean
     * @return bool|int
     * @author Tioncico
     * Time: 19:49
     */
    public function generateController()
    {
        $realTableName = $this->setRealTableName();
        $phpNamespace = new PhpNamespace($this->config->getBaseNamespace());
        $phpNamespace->addUse($this->config->getModelClass());
        $phpNamespace->addUse(Status::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse(Mysql::class);
        $phpNamespace->addUse($this->config->getExtendClass());
        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->addExtend($this->config->getExtendClass());
        $phpClass->addComment("{$this->config->getTableComment()}");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        $this->addAddDataMethod($phpClass);
        $this->addUpdateDataMethod($phpClass);
        $this->addGetOneDataMethod($phpClass);
        $this->addGetAllDataMethod($phpClass);
        $this->addDeleteDataMethod($phpClass);

        return $this->createPHPDocument($this->config->getBaseDirectory() . '/' . $realTableName, $phpNamespace, $this->config->getTableColumns());
    }

    function validateGenerationStr()
    {
        $addColumnStr = '';
        $updateColumnStr = '';
        $getOneColumnStr = '';
        $getAllColumnStr = '';
        $deleteColumnStr = '';
        $updateColumnStr .= "        \$validate->addColumn('{$this->config->getPrimaryKey()}', 'id')->required();\n";
        $deleteColumnStr .= "        \$validate->addColumn('{$this->config->getPrimaryKey()}', 'id')->required();\n";
        $getOneColumnStr .= "        \$validate->addColumn('{$this->config->getPrimaryKey()}', 'id')->required();\n";
        $getAllColumnStr .= "        \$validate->addColumn('page', '页数')->optional();
        \$validate->addColumn('limit', 'limit')->optional();
        \$validate->addColumn('keyword', '关键词')->optional();";
        foreach ($this->config->getTableColumns() as $column) {
            if ($column['Key'] != 'PRI') {
                $addColumnStr .= "        \$validate->addColumn('{$column['Field']}', '{$column['Comment']}')";
                $updateColumnStr .= "        \$validate->addColumn('{$column['Field']}', '{$column['Comment']}')->optional();\n";
                if ($column['Null'] == 'NO') {
                    $addColumnStr .= "->required()";
                } else {
                    $addColumnStr .= "->optional()";
                }
                $addColumnStr .= ";\n";
            }
        }
        $body = '';
        $body .= <<<BODY
    case 'add':
        \$validate = new Validate();       
$addColumnStr
        break;
    case 'update':
        \$validate = new Validate();       
$updateColumnStr
        break;
    case 'getAll':
        \$validate = new Validate();       
$getAllColumnStr
        break;
    case 'getOne':
        \$validate = new Validate();       
$getOneColumnStr
        break;
    case 'delete':
        \$validate = new Validate();       
$deleteColumnStr
        break;
BODY;
       return $body;
    }


    function addAddDataMethod(ClassType $phpClass)
    {
        $addData = [];
        $method = $phpClass->addMethod('add');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
//        var_dump($this->config->getBaseNamespace(),$apiUrl);die;
        //配置基础注释
        $method->addComment("@api {get|post} {$apiUrl}/{$this->setRealTableName()}/add");
        $method->addComment("@apiName add");
        $method->addComment("@apiGroup {$apiUrl}/{$this->setRealTableName()}");
        $method->addComment("@apiPermission {$this->config->getAuthName()}");
        $method->addComment("@apiDescription add新增数据");
        $this->config->getAuthSessionName() && ($method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token"));
        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);


        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$data = [

Body;
//        //注解注释
//        foreach ($this->config->getTableColumns() as $column) {
//            if ($column['Key'] != 'PRI') {
//                $addData[] = $column['Field'];
//                $columnType = $this->convertDbTypeToDocType($column['Type']);
//                if ($column['Null'] == 'NO') {
//                    $method->addComment("@Param(name=\"{$column['Field']}\", alias=\"{$column['Comment']}\", required=\"\", lengthMax=\"32\")");
//                } else {
//                    $method->addComment("@apiParam {{$columnType}} [{$column['Field']}] {$column['Comment']}");
//                }
//                $methodBody.="    '{$column['Field']}'=>\$param['{$column['Field']}'],\n";
//            } else {
//                $this->config->setPrimaryKey($column['Field']);
//            }
//        }
        //api doc注释
        foreach ($this->config->getTableColumns() as $column) {
            if ($column['Key'] != 'PRI') {
                $addData[] = $column['Field'];
                $columnType = $this->convertDbTypeToDocType($column['Type']);
                if ($column['Null'] == 'NO') {
                    $method->addComment("@apiParam {{$columnType}} {$column['Field']} {$column['Comment']}");
                } else {
                    $method->addComment("@apiParam {{$columnType}} [{$column['Field']}] {$column['Comment']}");
                }
                $methodBody.="    '{$column['Field']}'=>\$param['{$column['Field']}'],\n";
            } else {
                $this->config->setPrimaryKey($column['Field']);
            }
        }
        $methodBody .= <<<Body
];
\$model = new {$modelName}(\$data);
\$rs = \$model->save();
if (\$rs) {
    \$this->writeJson(Status::CODE_OK, \$model->toArray(), "success");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], \$model->lastQueryResult()->getLastError());
}
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"success\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addUpdateDataMethod(ClassType $phpClass)
    {
        $addData = [];
        $method = $phpClass->addMethod('update');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@api {get|post} {$apiUrl}/{$this->setRealTableName()}/update");
        $method->addComment("@apiName update");
        $method->addComment("@apiGroup {$apiUrl}/{$this->setRealTableName()}");
        $method->addComment("@apiPermission {$this->config->getAuthName()}");
        $method->addComment("@apiDescription update修改数据");
        $this->config->getAuthSessionName() && ($method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token"));
        $method->addComment("@apiParam {int} {$this->config->getPrimaryKey()} 主键id");
        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$this->config->getPrimaryKey()}' => \$param['{$this->config->getPrimaryKey()}']]);
if (empty(\$info)) {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
    return false;
}
\$updateData = [];
\n
Body;
        foreach ($this->config->getTableColumns() as $column) {
            if ($column['Key'] != 'PRI') {
                $addData[] = $column['Field'];
                $columnType = $this->convertDbTypeToDocType($column['Type']);
                $method->addComment("@apiParam {{$columnType}} [{$column['Field']}] {$column['Comment']}");
                $methodBody.="\$updateData['{$column['Field']}'] = \$param['{$column['Field']}']??\$info->{$column['Field']};\n";
            } else {
                $this->config->setPrimaryKey($column['Field']);
            }
        }
        $methodBody .= <<<Body
\$rs = \$info->update(\$updateData);
if (\$rs) {
    \$this->writeJson(Status::CODE_OK, \$rs, "success");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], \$model->lastQueryResult()->getLastError());
}
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"success\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addGetOneDataMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('getOne');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@api {get|post} {$apiUrl}/{$this->setRealTableName()}/getOne");
        $method->addComment("@apiName getOne");
        $method->addComment("@apiGroup {$apiUrl}/{$this->setRealTableName()}");
        $method->addComment("@apiPermission {$this->config->getAuthName()}");
        $method->addComment("@apiDescription 根据主键获取一条信息");
        $this->config->getAuthSessionName() && ($method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token"));
        $method->addComment("@apiParam {int} {$this->config->getPrimaryKey()} 主键id");
        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$bean = \$model->get(['{$this->config->getPrimaryKey()}' => \$param['{$this->config->getPrimaryKey()}']]);
if (\$bean) {
    \$this->writeJson(Status::CODE_OK, \$bean, "success");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
}
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"success\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addDeleteDataMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('delete');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@api {get|post} {$apiUrl}/{$this->setRealTableName()}/delete");
        $method->addComment("@apiName delete");
        $method->addComment("@apiGroup {$apiUrl}/{$this->setRealTableName()}");
        $method->addComment("@apiPermission {$this->config->getAuthName()}");
        $method->addComment("@apiDescription 根据主键删除一条信息");
        $this->config->getAuthSessionName() && ($method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token"));
        $method->addComment("@apiParam {int} {$this->config->getPrimaryKey()} 主键id");
        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();

\$rs = \$model->destroy(['{$this->config->getPrimaryKey()}' => \$param['{$this->config->getPrimaryKey()}']]);
if (\$rs) {
    \$this->writeJson(Status::CODE_OK, [], "success");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
}
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"success\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addGetAllDataMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('getAll');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@api {get|post} {$apiUrl}/{$this->setRealTableName()}/getAll");
        $method->addComment("@apiName getAll");
        $method->addComment("@apiGroup {$apiUrl}/{$this->setRealTableName()}");
        $method->addComment("@apiPermission {$this->config->getAuthName()}");
        $method->addComment("@apiDescription 获取一个列表");
        $this->config->getAuthSessionName() && ($method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token"));
        $method->addComment("@apiParam {String} [page=1]");
        $method->addComment("@apiParam {String} [limit=20]");
        $method->addComment("@apiParam {String} [keyword] 关键字,根据表的不同而不同");
        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$page = (int)(\$param['page']??1);
\$limit = (int)(\$param['limit']??20);
\$model = new {$modelName}();
\$data = \$model->getAll(\$page, \$param['keyword']??null, \$limit);
\$this->writeJson(Status::CODE_OK, \$data, 'success');
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"success\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }


    /**
     * 处理表真实名称
     * setRealTableName
     * @return bool|mixed|string
     * @author tioncico
     * Time: 下午11:55
     */
    function setRealTableName()
    {
        if ($this->config->getRealTableName()) {
            return $this->config->getRealTableName();
        }
        //先去除前缀
        $tableName = substr($this->config->getTableName(), strlen($this->config->getTablePre()));
        //去除后缀
        foreach ($this->config->getIgnoreString() as $string) {
            $tableName = rtrim($tableName, $string);
        }
        //下划线转驼峰,并且首字母大写
        $tableName = ucfirst(Str::camel($tableName));
        $this->config->setRealTableName($tableName);
        return $tableName;
    }
    function getColumnDeaflutValue($column)
    {

    }

    /**
     * convertDbTypeToDocType
     * @param $fieldType
     * @return string
     * @author Tioncico
     * Time: 19:49
     */
    protected function convertDbTypeToDocType($fieldType)
    {
        $newFieldType = strtolower(strstr($fieldType, '(', true));
        if ($newFieldType == '') $newFieldType = strtolower($fieldType);
        if (in_array($newFieldType, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {
            $newFieldType = 'int';
        } elseif (in_array($newFieldType, ['float', 'double', 'real', 'decimal', 'numeric'])) {
            $newFieldType = 'float';
        } elseif (in_array($newFieldType, ['char', 'varchar', 'text'])) {
            $newFieldType = 'string';
        } else {
            $newFieldType = 'mixed';
        }
        return $newFieldType;
    }

    /**
     * createPHPDocument
     * @param $fileName
     * @param $fileContent
     * @param $tableColumns
     * @return bool|int
     * @author Tioncico
     * Time: 19:49
     */
    protected function createPHPDocument($fileName, $fileContent, $tableColumns)
    {
        $content = "<?php\n\n{$fileContent}\n";
        $result = file_put_contents($fileName . '.php', $content);

        return $result == false ? $result : $fileName . '.php';
    }


}
