<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:57
 */

namespace AutomaticGeneration;


use AutomaticGeneration\Config\BaseConfig;
use EasySwoole\Utility\File;
use Nette\PhpGenerator\PhpNamespace;

abstract class GenerationBase
{
    /**
     * @var $config BaseConfig;
     */
    protected $config;
    protected $phpClass;
    protected $phpNamespace;

    /**
     * BeanBuilder constructor.
     * @param        $config
     * @throws \Exception
     */
    public function __construct(BaseConfig $config)
    {
        $this->config = $config;
        File::createDirectory($config->getBaseDirectory());
        $phpNamespace = new PhpNamespace($this->config->getBaseNamespace());
        $this->phpNamespace = $phpNamespace;
        $className = $this->getClassName();
        $phpClass = $phpNamespace->addClass($className);
        $phpNamespace->addUse($this->config->getExtendClass());
        $phpClass->addExtend($this->config->getExtendClass());
        $this->phpClass = $phpClass;
    }

    abstract function getClassName();

    abstract function addClassData();

    function generate()
    {
        $this->addClassData();
        return $this->createPHPDocument();
    }


    /**
     * createPHPDocument
     * @return bool|int
     * @author Tioncico
     * Time: 19:49
     */
    protected function createPHPDocument()
    {
        $fileName = $this->config->getBaseDirectory() . '/' . $this->getClassName();
        $content = "<?php\n\n{$this->phpNamespace}\n";
        $result = file_put_contents($fileName . '.php', $content);

        return $result == false ? $result : $fileName . '.php';
    }
}