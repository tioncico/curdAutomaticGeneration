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
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\Utility\File;
use EasySwoole\Utility\Str;
use EasySwoole\Validate\Validate;
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

    protected function addUse(PhpNamespace $phpNamespace)
    {
        $phpNamespace->addUse($this->config->getModelClass());
        $phpNamespace->addUse(Status::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse($this->config->getExtendClass());
        //引入新版注解,以及文档生成
        $phpNamespace->addUse(ApiFail::class);
        $phpNamespace->addUse(ApiRequestExample::class);
        $phpNamespace->addUse(ApiSuccess::class);
        $phpNamespace->addUse(Method::class);
        $phpNamespace->addUse(Param::class);
        $phpNamespace->addUse(Api::class);
        $phpNamespace->addUse(ResponseParam::class);
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
        $table = $this->config->getTable();
        $phpNamespace = new PhpNamespace($this->config->getBaseNamespace());

        $this->addUse($phpNamespace);

        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->addExtend($this->config->getExtendClass());
        $phpClass->addComment("{$table->getComment()}");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');

        $this->addAddDataMethod($phpClass);
        $this->addUpdateDataMethod($phpClass);
        $this->addGetOneDataMethod($phpClass);
        $this->addListDataMethod($phpClass);
        $this->addDeleteDataMethod($phpClass);

        return $this->createPHPDocument($this->config->getBaseDirectory() . '/' . $realTableName, $phpNamespace);
    }

    function addAddDataMethod(ClassType $phpClass)
    {
        $methodName = 'add';
        $table = $this->config->getTable();
        $addData = [];
        $method = $phpClass->addMethod('add');
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());

        //配置基础注释
        $method->addComment("@Api(name=\"{$methodName}\",group=\"{$apiUrl}/{$this->setRealTableName()}\",description=\"add新增数据\",path=\"{$apiUrl}/{$this->setRealTableName()}/{$methodName}\")");
        $method->addComment("@Method(allow={GET,POST})");

        if ($this->config->getAuthSessionName()) {
            $method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token");
            $method->addComment("@Param(name=\"{$this->config->getAuthSessionName()}\", from={COOKIE,GET,POST}, alias=\"权限验证token\" required=\"\")");
        }

        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);


        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$data = [

Body;
        //注解注释
        foreach ($table->getColumns() as $column) {
            $addData[] = $column->getColumnName();
            $columnName = $column->getColumnName();
            $columnComment = $column->getColumnComment();
            $paramValue = new \EasySwoole\HttpAnnotation\AnnotationTag\Param();
            $paramValue->name = $columnName;
            $paramValue->alias = $columnComment;
            $paramValue->description = $columnComment;
            $paramValue->lengthMax = $column->getColumnLimit();
            $paramValue->required = null;
            $paramValue->optional = null;
            $paramValue->defaultValue = $column->getDefaultValue();
            if ($column->isNotNull()) {
                $paramValue->required = '';
                $methodBody .= "    '{$columnName}'=>\$param['{$columnName}'],\n";
            } else {
                $paramValue->optional = '';
                $methodBody .= "    '{$columnName}'=>\$param['{$columnName}'] ?? '',\n";
            }
            $this->addColumnComment($method, $paramValue);
        }


        $methodBody .= <<<Body
];
\$model = new {$modelName}(\$data);
\$model->save();
\$this->writeJson(Status::CODE_OK, \$model->toArray(), "新增成功");

Body;
        $method->setBody($methodBody);
        $method->addComment("@ResponseParam(name=\"code\",description=\"状态码\")");
        $method->addComment("@ResponseParam(name=\"result\",description=\"api请求结果\")");
        $method->addComment("@ResponseParam(name=\"msg\",description=\"api提示信息\")");
        $method->addComment("@ApiSuccess({\"code\":200,\"result\":[],\"msg\":\"新增成功\"})");
        $method->addComment("@ApiFail({\"code\":400,\"result\":[],\"msg\":\"errorMsg\"})");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addUpdateDataMethod(ClassType $phpClass)
    {
        $table = $this->config->getTable();
        $methodName = 'update';
        $addData = [];
        $method = $phpClass->addMethod($methodName);
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());

        //配置基础注释
        $method->addComment("@Api(name=\"{$methodName}\",group=\"{$apiUrl}/{$this->setRealTableName()}\",description=\"update更新数据\",path=\"{$apiUrl}/{$this->setRealTableName()}/{$methodName}\")");
        $method->addComment("@Method(allow={GET,POST})");

        if ($this->config->getAuthSessionName()) {
            $method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token");
            $method->addComment("@Param(name=\"{$this->config->getAuthSessionName()}\", from={COOKIE,GET,POST}, alias=\"权限验证token\" required=\"\")");
        }

        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (empty(\$info)) {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
    return false;
}
\$updateData = [];
\n
Body;
        //注解注释
        foreach ($table->getColumns() as $column) {
            $addData[] = $column->getColumnName();
            $columnName = $column->getColumnName();
            $columnComment = $column->getColumnComment();
            $paramValue = new Param();
            $paramValue->name = $columnName;
            $paramValue->alias = $columnComment;
            $paramValue->description = $columnComment;
            $paramValue->lengthMax = $column->getColumnLimit();
            $paramValue->required = null;
            $paramValue->optional = null;
            $paramValue->defaultValue = $column->getDefaultValue();
            $paramValue->optional = '';
            if ($columnName == $table->getPkFiledName()) {
                $paramValue->required = '';
                $paramValue->optional = null;
            }
            $methodBody .= "\$updateData['{$columnName}']=\$param['{$columnName}'] ?? \$info->{$columnName};\n";
            $this->addColumnComment($method, $paramValue);
        }
        $methodBody .= <<<Body
\$info->update(\$updateData);
\$this->writeJson(Status::CODE_OK, \$info, "更新数据成功");

Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"更新数据成功\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addGetOneDataMethod(ClassType $phpClass)
    {
        $table = $this->config->getTable();
        $methodName = 'getOne';
        $method = $phpClass->addMethod($methodName);
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@Api(name=\"{$methodName}\",group=\"{$apiUrl}/{$this->setRealTableName()}\",description=\"获取一条数据\",path=\"{$apiUrl}/{$this->setRealTableName()}/{$methodName}\")");
        $method->addComment("@Method(allow={GET,POST})");

        if ($this->config->getAuthSessionName()) {
            $method->addComment("@apiParam {String}  {$this->config->getAuthSessionName()} 权限验证token");
            $method->addComment("@Param(name=\"{$this->config->getAuthSessionName()}\", from={COOKIE,GET,POST}, alias=\"权限验证token\" required=\"\")");
        }


        //注解注释
        foreach ($table->getColumns() as $column) {
            $addData[] = $column->getColumnName();
            $columnName = $column->getColumnName();
            $columnComment = $column->getColumnComment();
            $paramValue = new Param();
            $paramValue->name = $columnName;
            $paramValue->alias = $columnComment;
            $paramValue->description = $columnComment;
            $paramValue->lengthMax = $column->getColumnLimit();
            $paramValue->required = null;
            $paramValue->optional = null;
            $paramValue->defaultValue = $column->getDefaultValue();
            if ($columnName != $table->getPkFiledName()) {
                continue;
            }
            $paramValue->required = '';
            $this->addColumnComment($method, $paramValue);
            break;
        }


        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (\$info) {
    \$this->writeJson(Status::CODE_OK, \$info, "获取数据成功.");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], '数据不存在');
}
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"获取数据成功\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addDeleteDataMethod(ClassType $phpClass)
    {
        $table = $this->config->getTable();
        $methodName = 'delete';
        $method = $phpClass->addMethod($methodName);
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@Api(name=\"{$methodName}\",group=\"{$apiUrl}/{$this->setRealTableName()}\",description=\"删除一条数据\",path=\"{$apiUrl}/{$this->setRealTableName()}/{$methodName}\")");
        $method->addComment("@Method(allow={GET,POST})");

        if ($this->config->getAuthSessionName()) {
            $method->addComment("@Param(name=\"{$this->config->getAuthSessionName()}\", from={COOKIE,GET,POST}, alias=\"权限验证token\" required=\"\")");
        }
        //注解注释
        foreach ($table->getColumns() as $column) {
            $addData[] = $column->getColumnName();
            $columnName = $column->getColumnName();
            $columnComment = $column->getColumnComment();
            $paramValue = new Param();
            $paramValue->name = $columnName;
            $paramValue->alias = $columnComment;
            $paramValue->description = $columnComment;
            $paramValue->lengthMax = $column->getColumnLimit();
            $paramValue->required = null;
            $paramValue->optional = null;
            $paramValue->defaultValue = $column->getDefaultValue();
            if ($columnName != $table->getPkFiledName()) {
                continue;
            }
            $paramValue->required = '';
            $this->addColumnComment($method, $paramValue);
            break;
        }

        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (!\$info) {
    \$this->writeJson(Status::CODE_OK, \$info, "数据不存在.");
}

\$info->destroy();
\$this->writeJson(Status::CODE_OK, [], "删除成功.");
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"删除成功.\"}");
        $method->addComment("@author: AutomaticGeneration < 1067197739@qq.com >");
    }

    function addListDataMethod(ClassType $phpClass)
    {
        $methodName = 'list';
        $method = $phpClass->addMethod($methodName);
        $apiUrl = str_replace(['App\\HttpController', '\\'], ['', '/'], $this->config->getBaseNamespace());
        //配置基础注释
        $method->addComment("@Api(name=\"{$methodName}\",group=\"{$apiUrl}/{$this->setRealTableName()}\",description=\"获取数据列表\",path=\"{$apiUrl}/{$this->setRealTableName()}/{$methodName}\")");
        $method->addComment("@Method(allow={GET,POST})");

        if ($this->config->getAuthSessionName()) {
            $method->addComment("@Param(name=\"{$this->config->getAuthSessionName()}\", from={COOKIE,GET,POST}, alias=\"权限验证token\" required=\"\")");
        }

        $modelNameArr = (explode('\\', $this->config->getModelClass()));
        $modelName = end($modelNameArr);
        //新增page参数注解
        $method->addComment("@Param(name=\"page\", from={GET,POST}, alias=\"页数\" optional=\"\")");
        $method->addComment("@Param(name=\"pageSize\", from={GET,POST}, alias=\"每页总数\" optional=\"\")");


        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$page = (int)(\$param['page']??1);
\$limit = (int)(\$param['limit']??20);
\$model = new {$modelName}();
\$data = \$model->getAll(\$page, \$limit);
\$this->writeJson(Status::CODE_OK, \$data, '获取列表成功');
Body;
        $method->setBody($methodBody);
        $method->addComment("@apiSuccess {Number} code");
        $method->addComment("@apiSuccess {Object[]} data");
        $method->addComment("@apiSuccess {String} msg");
        $method->addComment("@apiSuccessExample {json} Success-Response:");
        $method->addComment("HTTP/1.1 200 OK");
        $method->addComment("{\"code\":200,\"data\":{},\"msg\":\"获取列表成功\"}");
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
        $tableName = substr($this->config->getTable()->getTable(), strlen($this->config->getTablePre()));
        //去除后缀
        foreach ($this->config->getIgnoreString() as $string) {
            $tableName = rtrim($tableName, $string);
        }
        //下划线转驼峰,并且首字母大写
        $tableName = ucfirst(Str::camel($tableName));
        $this->config->setRealTableName($tableName);
        return $tableName;
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
     * @return bool|int
     * @author Tioncico
     * Time: 19:49
     */
    protected function createPHPDocument($fileName, $fileContent)
    {
        $content = "<?php\n\n{$fileContent}\n";
        $result = file_put_contents($fileName . '.php', $content);

        return $result == false ? $result : $fileName . '.php';
    }

    /**
     * 新增参数注释
     * addColumnComment
     * @param \Nette\PhpGenerator\Method                     $method
     * @param \EasySwoole\HttpAnnotation\AnnotationTag\Param $param
     * @author Tioncico
     * Time: 9:49
     */
    protected function addColumnComment(\Nette\PhpGenerator\Method $method, \EasySwoole\HttpAnnotation\AnnotationTag\Param $param)
    {
        $commentStr = "@Param(name=\"{$param->name}\"";
        $arr = ['alias', 'description', 'lengthMax', 'required', 'optional', 'defaultValue'];
        foreach ($arr as $value) {
            if ($param->$value !== null) {
                $commentStr .= ",$value=\"{$param->$value}\"";
            }
        }
        $commentStr .= ")";
        $method->addComment($commentStr);
    }
}
