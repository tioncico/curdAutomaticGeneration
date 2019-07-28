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
        $phpNamespace = new PhpNamespace($this->config->getBaseNamespace());
        $realTableName = $this->setRealTableName() . 'Model';
        $phpClass = $this->addClassBaseContent($this->config->getTableName(), $realTableName, $phpNamespace, $this->config->getTableComment(), $this->config->getTableColumns());
        //配置getAll
        $this->addGetAllMethod($phpClass);
        $this->addGetOneMethod($phpClass, $this->config->getTableName(), $this->config->getTableColumns());
        $this->addAddMethod($phpClass, $this->config->getTableName(), $this->config->getTableColumns());
        $this->addDeleteMethod($phpClass, $this->config->getTableName(), $this->config->getTableColumns());
        $this->addUpdateMethod($phpClass, $this->config->getTableName(), $this->config->getTableColumns());
        //配置根据索引来查询的方法项
        $indexList = $this->getIndexList($this->config->getTableColumns());
        foreach ($indexList as $index) {
            $this->addIndexGetAllMethod($phpClass, $index);
            $this->addIndexGetOneMethod($phpClass, $index);
        }

        return $this->createPHPDocument($this->config->getBaseDirectory() . '/' . $realTableName, $phpNamespace, $this->config->getTableColumns());
    }

    protected function getIndexList($columns)
    {
        $list = [];
        foreach ($columns as $column) {
            if ($column['Key'] == 'MUL') {
                $list[] = $column;
            }
        }
        return $list;
    }

    /**
     * 处理表真实名称
     * setRealTableName
     * @return bool|mixed|string
     * @author tioncico
     * Time: 下午11:55
     */
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
        var_dump($tableName);
        return $tableName;
    }

    /**
     * 新增基础类内容
     * addClassBaseContent
     * @param $tableName
     * @param $realTableName
     * @param $phpNamespace
     * @param $tableComment
     * @return ClassType
     * @author Tioncico
     * Time: 21:38
     */
    protected function addClassBaseContent($tableName, $realTableName, $phpNamespace, $tableComment, $tableColumns): ClassType
    {
        $phpClass = $phpNamespace->addClass($realTableName);
        //配置类基本信息
        if ($this->config->getExtendClass()) {
            $phpClass->addExtend($this->config->getExtendClass());
        }
        $phpClass->addComment("{$tableComment}");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        //配置表名属性
        $phpClass->addProperty('table', $tableName)
            ->setVisibility('protected');
        foreach ($tableColumns as $column) {
            if ($column['Key'] == 'PRI') {
                $this->config->setPrimaryKey($column['Field']);
                $phpClass->addProperty('primaryKey', $column['Field'])
                    ->setVisibility('protected');
                break;
            }
        }
        return $phpClass;
    }

    protected function addUpdateMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('update');
        $beanName = $this->setRealTableName() . 'Bean';;
        $namespaceBeanName = $this->config->getBaseNamespace() . '\\' . $beanName;
        //配置基础注释
        $method->addComment("默认根据主键({$this->config->getPrimaryKey()})进行更新");
        $method->addComment("@delete");
        $method->addComment("@param  {$beanName} \$bean");//默认为使用Bean注释
        $method->addComment("@param  array \$data");

        //配置返回类型
        $method->setReturnType('bool');
        //配置参数为bean
        $method->addParameter('bean')->setTypeHint($namespaceBeanName);
        $method->addParameter('data')->setTypeHint('array');
        $getPrimaryKeyMethodName = "get" . Str::studly($this->config->getPrimaryKey());

        $methodBody = <<<Body
if (empty(\$data)){
    return false;
}
return \$this->getDb()->where(\$this->primaryKey, \$bean->$getPrimaryKeyMethodName())->update(\$this->table, \$data);
Body;
        $method->setBody($methodBody);
        $method->addComment("@return bool");
    }

    protected function addDeleteMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('delete');
        $beanName = $this->setRealTableName() . 'Bean';;
        $namespaceBeanName = $this->config->getBaseNamespace() . '\\' . $beanName;
        //配置基础注释
        $method->addComment("默认根据主键({$this->config->getPrimaryKey()})进行删除");
        $method->addComment("@delete");
        $method->addComment("@param  {$beanName} \$bean");//默认为使用Bean注释

        //配置返回类型
        $method->setReturnType('bool');
        //配置参数为bean
        $method->addParameter('bean')->setTypeHint($namespaceBeanName);
        $getPrimaryKeyMethodName = "get" . Str::studly($this->config->getPrimaryKey());

        $methodBody = <<<Body
return  \$this->getDb()->where(\$this->primaryKey, \$bean->$getPrimaryKeyMethodName())->delete(\$this->table);
Body;
        $method->setBody($methodBody);
        $method->addComment("@return bool");
    }

    protected function addAddMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('add');
        $beanName = $this->setRealTableName() . 'Bean';;
        $namespaceBeanName = $this->config->getBaseNamespace() . '\\' . $beanName;
        //配置基础注释
        $method->addComment("默认根据bean数据进行插入数据");
        $method->addComment("@add");
        $method->addComment("@param  {$beanName} \$bean");//默认为使用Bean注释
        //配置参数为bean
        $method->addParameter('bean')->setTypeHint($namespaceBeanName);
        //配置返回类型
        $method->setReturnType('bool');

        $methodBody = <<<Body
return \$this->getDb()->insert(\$this->table, \$bean->toArray(null, \$bean::FILTER_NOT_NULL));
Body;
        $method->setBody($methodBody);
        $method->addComment("@return bool");
    }

    protected function addGetOneMethod(ClassType $phpClass)
    {
        $method = $phpClass->addMethod('getOne');
        $beanName = $this->setRealTableName() . 'Bean';;
        $namespaceBeanName = $this->config->getBaseNamespace() . '\\' . $beanName;
        //配置基础注释
        $method->addComment("默认根据主键({$this->config->getPrimaryKey()})进行搜索");
        $method->addComment("@getOne");
        $method->addComment("@param  {$beanName} \$bean");//默认为使用Bean注释
        $method->addComment("@param  string \$field");//默认为使用Bean注释

        //配置返回类型
        $method->setReturnType($namespaceBeanName)->setReturnNullable();
        //配置参数为bean
        $method->addParameter('bean')->setTypeHint($namespaceBeanName);
        $method->addParameter('field', '*')->setTypeHint('string');
        $getPrimaryKeyMethodName = "get" . Str::studly($this->config->getPrimaryKey());

        $methodBody = <<<Body
\$info = \$this->getDb()->where(\$this->primaryKey, \$bean->$getPrimaryKeyMethodName())->getOne(\$this->table,\$field);
if (empty(\$info)) {
    return null;
}
return new $beanName(\$info);
Body;
        $method->setBody($methodBody);
        $method->addComment("@return $beanName");
    }

    protected function addIndexGetOneMethod(ClassType $phpClass, $column)
    {
        $method = $phpClass->addMethod('getOneBy' . Str::studly($column['Field']));
        $beanName = $this->setRealTableName() . 'Bean';;
        $namespaceBeanName = $this->config->getBaseNamespace() . '\\' . $beanName;
        //配置基础注释
        $method->addComment("根据索引({$column['Field']})进行搜索");
        $method->addComment("@getOne");
        $method->addComment("@param  " . $this->convertDbTypeToDocType($column['Type']) . " \${$column['Field']}");//默认为使用Bean注释
        $method->addComment("@param  string \$field");//默认为使用Bean注释

        //配置返回类型
        $method->setReturnType($namespaceBeanName)->setReturnNullable();
        //配置参数为bean
        $method->addParameter($column['Field']);
        $method->addParameter('field', '*')->setTypeHint('string');
        $getPrimaryKeyMethodName = "get" . Str::studly($this->config->getPrimaryKey());

        $methodBody = <<<Body
\$info = \$this->getDb()->where('{$column['Field']}', \${$column['Field']})->getOne(\$this->table,\$field);
if (empty(\$info)) {
    return null;
}
return new $beanName(\$info);
Body;
        $method->setBody($methodBody);
        $method->addComment("@return $beanName");
    }

    protected function addGetAllMethod(ClassType $phpClass, $keyword = '')
    {
        $method = $phpClass->addMethod('getAll');
        if (empty($keyword)) {
            echo "(getAll)请输入搜索的关键字\n";
            $keyword = trim(fgets(STDIN));
        }
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
    \$this->getDb()->where('$keyword', '%' . \$keyword . '%', 'like');
}
Body;
        }

        $methodBody .= <<<Body
        
\$list = \$this->getDb()
    ->withTotalCount()
    ->orderBy(\$this->primaryKey, 'DESC')
    ->get(\$this->table, [\$pageSize * (\$page  - 1), \$pageSize],\$field);
\$total = \$this->getDb()->getTotalCount();
return ['total' => \$total, 'list' => \$list];
Body;
        //配置方法内容
        $method->setBody($methodBody);
        $method->addComment('@return array[total,list]');
    }

    protected function addIndexGetAllMethod(ClassType $phpClass, $column, $keyword = '')
    {
        $method = $phpClass->addMethod('getAllBy' . Str::studly($column['Field']));
        if (empty($keyword)) {
            echo "(getAllBy{$column['Field']})请输入搜索的关键字\n";
            $keyword = trim(fgets(STDIN));
        }
        //配置基础注释
        $method->addComment("@getAll{$column['Field']}");
        if (!empty($keyword)) {
            $method->addComment("@keyword $keyword");
        }
        //配置方法参数
        $method->addParameter($column['Field']);
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
            $method->addComment("@param  " . $parameter->getTypeHint() . '  ' . $parameter->getName() . '  ' . $parameter->getDefaultValue());
        }

        //配置返回类型
        $method->setReturnType('array');
        $methodBody = '';
        if (!empty($keyword)) {
            $methodBody .= <<<Body
if (!empty(\$keyword)) {
    \$this->getDb()->where('$keyword', '%' . \$keyword . '%', 'like');
}
Body;
        }

        $methodBody .= <<<Body
        
\$this->getDb()->where('{$column['Field']}',\${$column['Field']});

\$list = \$this->getDb()
    ->withTotalCount()
    ->orderBy(\$this->primaryKey, 'DESC')
    ->get(\$this->table, [\$pageSize * (\$page  - 1), \$pageSize],\$field);
\$total = \$this->getDb()->getTotalCount();
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
//        var_dump($fileName.'.php');
        if ($this->config->isConfirmWrite()) {
            if (file_exists($fileName . '.php')) {
                echo "(Model)当前路径已经存在文件,是否覆盖?(y/n)\n";
                if (trim(fgets(STDIN)) == 'n') {
                    echo "已结束运行\n";
                    return false;
                }
            }
        }
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