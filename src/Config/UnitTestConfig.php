<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-4-30
 * Time: 下午11:22
 */

namespace AutomaticGeneration\Config;


use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\Spl\SplBean;

class UnitTestConfig extends BaseConfig
{
    protected $controllerClass;
    protected $modelClass;

    /**
     * @return mixed
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param mixed $controllerClass
     */
    public function setControllerClass($controllerClass): void
    {
        $this->controllerClass = $controllerClass;
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


}