<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-4-30
 * Time: 下午11:22
 */

namespace AutomaticGeneration\Config;


use EasySwoole\Spl\SplBean;

class ModelConfig extends SplBean
{
    protected $tableName;//表名称
    protected $tableComment;//表注释
    protected $tableColumns;//表字段信息
    protected $realTableName;//表(生成的文件)真实名称
    protected $extendClass;//继承的基类
    protected $baseDirectory;//生成的目录
    protected $baseNamespace;//生成的命名空间
    protected $tablePre = '';//数据表前缀
    protected $keyword='';//getAll时的关键字
    protected $ignoreString = [
        'list',
        'log'
    ];//文件名生成时,忽略的字符串(list,log等)
    protected $primaryKey;

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
    public function setExtendClass($extendClass)
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
    public function setBaseDirectory($baseDirectory)
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
    public function setBaseNamespace($baseNamespace)
    {
        $this->baseNamespace = $baseNamespace;
        //设置下基础目录
        $pathArr = explode('\\', $baseNamespace);
        $app = array_shift($pathArr);
        if ($app == 'App') {
            $this->setBaseDirectory(EASYSWOOLE_ROOT . '/' . \AutomaticGeneration\AppLogic::getAppPath() . implode('/', $pathArr));
        }
    }

    /**
     * @return string
     */
    public function getTablePre()
    {
        return $this->tablePre;
    }

    /**
     * @param string $tablePre
     */
    public function setTablePre($tablePre)
    {
        $this->tablePre = $tablePre;
    }

    /**
     * @return array
     */
    public function getIgnoreString()
    {
        return $this->ignoreString;
    }

    /**
     * @param array $ignoreString
     */
    public function setIgnoreString($ignoreString)
    {
        $this->ignoreString = $ignoreString;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param mixed $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName): void
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getTableComment()
    {
        return $this->tableComment;
    }

    /**
     * @param mixed $tableComment
     */
    public function setTableComment($tableComment): void
    {
        $this->tableComment = $tableComment;
    }

    /**
     * @return mixed
     */
    public function getTableColumns()
    {
        return $this->tableColumns;
    }

    /**
     * @param mixed $tableColumns
     */
    public function setTableColumns($tableColumns): void
    {
        $this->tableColumns = $tableColumns;
    }

    /**
     * @return mixed
     */
    public function getRealTableName()
    {
        return $this->realTableName;
    }

    /**
     * @param mixed $realTableName
     */
    public function setRealTableName($realTableName): void
    {
        $this->realTableName = $realTableName;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }


}