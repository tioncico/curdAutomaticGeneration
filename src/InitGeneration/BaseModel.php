<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-26
 * Time: 下午11:16
 */

namespace AutomaticGeneration\InitGeneration;


use AutomaticGeneration\Config\BaseConfig;
use AutomaticGeneration\GenerationBase;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

class BaseModel extends GenerationBase
{
    public function __construct(?BaseConfig $config = null)
    {
        if (empty($config)) {
            $config = new BaseConfig();
            $config->setExtendClass(AbstractModel::class);
            $config->setBaseNamespace("App\\Model");
        }
        parent::__construct($config);
    }

    function getClassName()
    {
        return "BaseModel";
    }

    function addClassData()
    {
        $this->phpNamespace->addUse(DbManager::class);
        $method = $this->phpClass->addMethod('transaction');
        $method->setStatic();
        $method->setParameters('callable')->setReturnType('callable');
        $method->setBody(<<<BODY
try {
    DbManager::getInstance()->startTransaction();
    \$result = \$callable();
    DbManager::getInstance()->commit();
    return \$result;
} catch (\Throwable \$throwable) {
    DbManager::getInstance()->rollback();
    throw \$throwable;;
}
BODY
        );
    }


}