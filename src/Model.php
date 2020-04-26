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
class Model extends GenerationBase
{
    /**
     * @var $config ModelConfig
     */
    protected $config;
    protected $className;

    function addClassData()
    {
        $this->addClassBaseContent();
        //配置getAll
        $this->addGetAllMethod();
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
    protected function addClassBaseContent(): ClassType
    {
        $table = $this->config->getTable();
        $phpClass = $this->phpClass;
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
     * @author Tioncico
     * Time: 10:52
     */
    protected function addGetAllMethod()
    {
        $phpClass = $this->phpClass;
        $method = $phpClass->addMethod('getAll');

        //配置返回类型
        $method->setReturnType('array');

        //配置方法参数
        $method->addParameter('page', 1)
            ->setTypeHint('int');
        $method->addParameter('pageSize', 10)
            ->setTypeHint('int');
        $method->addParameter('field', '*')->setTypeHint('string');

        $methodBody = '';
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
     * @return mixed
     */
    public function getClassName()
    {
        $className = $this->config->getRealTableName() . 'Model';
        return $className;
    }

}