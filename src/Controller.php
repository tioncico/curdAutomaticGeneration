<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-5-2
 * Time: 上午10:38
 */

namespace AutomaticGeneration;

use AutomaticGeneration\Config\BaseConfig;
use AutomaticGeneration\Config\ControllerConfig;
use AutomaticGeneration\ControllerMethodGeneration\Add;
use AutomaticGeneration\ControllerMethodGeneration\Delete;
use AutomaticGeneration\ControllerMethodGeneration\GetList;
use AutomaticGeneration\ControllerMethodGeneration\GetOne;
use AutomaticGeneration\ControllerMethodGeneration\MethodAbstract;
use AutomaticGeneration\ControllerMethodGeneration\Update;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\Validate\Validate;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * easyswoole 控制器快速构建器
 * Class ControllerBuilder
 * @package AutomaticGeneration
 */
class Controller extends GenerationBase
{
    /**
     * @var $config ControllerConfig
     */
    protected $config;
    protected $generationMethodList=[];

    function __construct(BaseConfig $config)
    {
        parent::__construct($config);
        $phpClass = $this->phpClass;
        $this->addGenerationMethod((new Add($this->config, $phpClass)));
        $this->addGenerationMethod((new Update($this->config, $phpClass)));
        $this->addGenerationMethod((new GetOne($this->config, $phpClass)));
        $this->addGenerationMethod((new GetList($this->config, $phpClass)));
        $this->addGenerationMethod((new Update($this->config, $phpClass)));
        $this->addGenerationMethod((new Delete($this->config, $phpClass)));
    }

    function getClassName()
    {
        return $this->config->getRealTableName();
    }


    protected function addUse(PhpNamespace $phpNamespace)
    {
        $phpNamespace->addUse($this->config->getModelClass());
        $phpNamespace->addUse(Status::class);
        $phpNamespace->addUse(Validate::class);
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
    public function addClassData()
    {
        $this->addUse($this->phpNamespace);
        /**
         * @var $method MethodAbstract
         */
        foreach ($this->generationMethodList as  $method){
            $method->run();
        }
        return $this->createPHPDocument();
    }

    function addGenerationMethod(MethodAbstract $abstract){
        $this->generationMethodList[$abstract->getMethodName()] = $abstract;
    }


}
