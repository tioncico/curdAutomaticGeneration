<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2018/11/10
 * Time: 上午1:52
 */

namespace AutomaticGeneration;

use AutomaticGeneration\Config\ModelConfig;
use EasySwoole\Utility\File;
use EasySwoole\Utility\Str;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * easyswoole model快速构建器
 * Class BeanBuilder
 * @package AutomaticGeneration
 */
class ModelBuilder
{
    /**
     * @var $config ModelConfig
     */
    protected $config;
    protected $className;

    /**
     * BeanBuilder constructor.
     * @param  $config
     * @throws \Exception
     */
    public function __construct(ModelConfig $config)
    {
        $this->config = $config;
        $this->createBaseDirectory($config->getBaseDirectory());
        $realTableName = $this->setRealTableName() . 'Model';
        $this->className = $this->config->getBaseNamespace() . '\\' . $realTableName;
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
    public function generateModel()
    {
        $table = $this->config->getTable();
        $phpNamespace = new PhpNamespace($this->config->getBaseNamespace());
        $realTableName = $this->setRealTableName() . 'Model';
        $phpClass = $this->addClassBaseContent($realTableName, $phpNamespace);

        //配置getAll
        $this->addGetAllMethod($phpClass);

//        foreach ($indexList as $index) {
//            $this->addIndexGetOneMethod($phpClass, $index);
//        }

        return $this->createPHPDocument($this->config->getBaseDirectory() . '/' . $realTableName, $phpNamespace);
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
     * 新增基础类内容
     * addClassBaseContent
     * @param $realTableName
     * @param $phpNamespace
     * @return ClassType
     * @author Tioncico
     * Time: 21:38
     */
    protected function addClassBaseContent($realTableName, PhpNamespace $phpNamespace): ClassType
    {
        $table = $this->config->getTable();
        $phpClass = $phpNamespace->addClass($realTableName);
        //配置类基本信息
        if ($this->config->getExtendClass()) {
            $phpClass->addExtend($this->config->getExtendClass());
        }
        $phpClass->addComment("{$table->getComment()}");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        //配置表名属性
        $phpClass->addProperty('tableName', $table->getTable())
            ->setVisibility('protected');
        foreach ($table->getColumns() as $column) {
            $name = $column->getColumnName();
            $comment = $column->getColumnComment();
            $columnType = $this->convertDbTypeToDocType($column->getColumnType());
            $phpClass->addComment("@property \${$name} {$columnType} | {$comment}");
        }
        return $phpClass;
    }

//    protected function addIndexGetOneMethod(ClassType $phpClass, $column)
//    {
//        $method = $phpClass->addMethod('getOneBy' . Str::studly($column['Field']));
//        $modelName = $this->setRealTableName() . 'Model';;
//        $namespaceModelName = $this->config->getBaseNamespace() . '\\' . $modelName;
//        //配置基础注释
//        $method->addComment("根据索引({$column['Field']})进行搜索");
//        $method->addComment("@getOne");
//        $method->addComment("@param  string \$field");//默认为使用Bean注释
//
//        //配置返回类型
//        $method->setReturnType($namespaceModelName)->setReturnNullable();
//        $method->addParameter('field', '*')->setTypeHint('string');
//
//        $methodBody = <<<Body
//\$info = \$this->where('{$column['Field']}', \$this->{$column['Field']})->field(\$field)->get();
//return \$info;
//Body;
//        $method->setBody($methodBody);
//        $method->addComment("@return $modelName|null");
//    }

    /**
     * addGetAllMethod
     * @param ClassType $phpClass
     * @author Tioncico
     * Time: 10:52
     */
    protected function addGetAllMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('getAll');
        $keyword = $this->config->getKeyword();

        //配置基础注释
        $method->addComment("@getAll");
        if (!empty($keyword)) {
            $method->addComment("@keyword $keyword");
        }
        //配置方法参数
        $method->addParameter('page', 1)
            ->setTypeHint('int');
        if (!empty($keyword)) {
            $method->addParameter('keyword', null)
                ->setTypeHint('string');
        }
        $method->addParameter('pageSize', 10)
            ->setTypeHint('int');
        $method->addParameter('field', '*')->setTypeHint('string');

        foreach ($method->getParameters() as $parameter) {
            $method->addComment("@param  " . $parameter->getTypeHint() . '  $' . $parameter->getName() . '  ' . $parameter->getDefaultValue());
        }

        //配置返回类型
        $method->setReturnType('array');

        $methodBody = '';
        if (!empty($keyword)) {
            $methodBody .= <<<Body
if (!empty(\$keyword)) {
    \$this->where('$keyword', '%' . \$keyword . '%', 'like');
}
Body;
        }

        $methodBody .= <<<Body
        
\$list = \$this
    ->withTotalCount()
	->order(\$this->schemaInfo()->getPkFiledName(), 'DESC')
    ->field(\$field)
    ->page(\$page, \$pageSize)
    ->all();
\$total = \$this->lastQueryResult()->getTotalCount();;
return ['total' => \$total, 'list' => \$list];
Body;
        //配置方法内容
        $method->setBody($methodBody);
        $method->addComment('@return array[total,list]');
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
        if (in_array($fieldType, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {
            $newFieldType = 'int';
        } elseif (in_array($fieldType, ['float', 'double', 'real', 'decimal', 'numeric'])) {
            $newFieldType = 'float';
        } elseif (in_array($fieldType, ['char', 'varchar', 'text'])) {
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
//        var_dump($content);
        $result = file_put_contents($fileName . '.php', $content);
        return $result == false ? $result : $fileName . '.php';
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

}