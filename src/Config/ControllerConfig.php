<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-5-1
 * Time: 上午9:31
 */

namespace AutomaticGeneration\Config;


class ControllerConfig extends BaseConfig
{
    protected $authName;//额外需要的授权用户分组
    protected $authSessionName;//额外需要的授权session名称
    protected $modelClass;//model的类名

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

}