<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午10:47
 */

namespace AutomaticGeneration\Config;


use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\Spl\SplBean;
use EasySwoole\Utility\Str;

class BaseConfig extends SplBean
{
    protected $extendClass;//继承的基类
    protected $baseDirectory;//生成的目录
    protected $baseNamespace;//生成的命名空间
    protected $tablePre = '';//数据表前缀
    /**
     * @var $table Table
     */
    protected $table;//表数据DDL对象
    protected $realTableName;//表(生成的文件)真实名称
    protected $ignoreString = [
        'list',
        'log'
    ];//文件名生成时,忽略的字符串(list,log等)

    /**
     * @return mixed
     */
    public function getExtendClass()
    {
        return $this->extendClass;
    }

    /**
     * @param mixed $extendClass
     */
    public function setExtendClass($extendClass): void
    {
        $this->extendClass = $extendClass;
    }

    /**
     * @return mixed
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * @param mixed $baseDirectory
     */
    public function setBaseDirectory($baseDirectory): void
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * @return mixed
     */
    public function getBaseNamespace()
    {
        return $this->baseNamespace;
    }

    /**
     * @param mixed $baseNamespace
     */
    public function setBaseNamespace($baseNamespace): void
    {
        $this->baseNamespace = $baseNamespace;
        //设置下基础目录
        $pathArr = explode('\\', $baseNamespace);
        $app = array_shift($pathArr);
        $this->setBaseDirectory(EASYSWOOLE_ROOT . '/' . \AutomaticGeneration\Unity::getNamespacePath($app) . implode('/', $pathArr));
    }

    /**
     * @return string
     */
    public function getTablePre(): string
    {
        return $this->tablePre;
    }

    /**
     * @param string $tablePre
     */
    public function setTablePre(string $tablePre): void
    {
        $this->tablePre = $tablePre;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param Table $table
     */
    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getRealTableName()
    {
        if ($this->realTableName) {
            return $this->realTableName;
        }
        //先去除前缀
        $tableName = substr($this->getTable()->getTable(), strlen($this->getTablePre()));
        //去除后缀
        foreach ($this->getIgnoreString() as $string) {
            $tableName = rtrim($tableName, $string);
        }
        //下划线转驼峰,并且首字母大写
        $realTableName = ucfirst(Str::camel($tableName));
        $this->setRealTableName($realTableName);
        return $realTableName;
    }

    /**
     * @param mixed $realTableName
     */
    public function setRealTableName($realTableName): void
    {
        $this->realTableName = $realTableName;
    }

    /**
     * @return array
     */
    public function getIgnoreString(): array
    {
        return $this->ignoreString;
    }

    /**
     * @param array $ignoreString
     */
    public function setIgnoreString(array $ignoreString): void
    {
        $this->ignoreString = $ignoreString;
    }


}