<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-5-1
 * Time: 上午9:31
 */

namespace AutomaticGeneration\Config;


use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\Spl\SplBean;

class ControllerConfig extends SplBean
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

    protected $authName;//额外需要的授权用户分组
    protected $authSessionName;//额外需要的授权session名称
    protected $modelClass;//model的类名


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
        $pathArr = explode('\\',$baseNamespace);
        $app = array_shift($pathArr);
        if ($app=='App'){
            $this->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . implode('/',$pathArr));
        }
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

    /**
     * @return mixed
     */
    public function getAuthName()
    {
        return $this->authName;
    }

    /**
     * @param mixed $authName
     */
    public function setAuthName($authName): void
    {
        $this->authName = $authName;
    }

    /**
     * @return mixed
     */
    public function getAuthSessionName()
    {
        return $this->authSessionName;
    }

    /**
     * @param mixed $authSessionName
     */
    public function setAuthSessionName($authSessionName): void
    {
        $this->authSessionName = $authSessionName;
    }

    /**
     * @return mixed
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @param mixed $modelClass
     */
    public function setModelClass($modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return mixed
     */
    public function getTable():Table
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table): void
    {
        $this->table = $table;
    }




}