<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-5-1
 * Time: 上午12:04
 */

namespace AutomaticGeneration;

/**
 * 初始化baseModel php
 */

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\Utility\File;
use Nette\PhpGenerator\PhpNamespace;

class Init
{
    protected $appPath;

    public function __construct(?string $appPath = null)
    {
        $this->appPath = EASYSWOOLE_ROOT . '/' . ($appPath??AppLogic::getAppPath());
    }

    function initBaseModel($poolObjectName = null)
    {
        $path = $this->appPath . '/Model';
        File::createDirectory($path);
        $realTableName = "BaseModel";

        $phpNamespace = new PhpNamespace("App\\Model");
        $phpNamespace->addUse(AbstractModel::class);
        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->setExtends(AbstractModel::class);
        $phpClass->addComment("BaseModel");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');

        return $this->createPHPDocument($this->appPath . '/Model/' . $realTableName, $phpNamespace);
    }

    function initBaseController($poolObjectName = null)
    {
        $path = $this->appPath . '/HttpController';
        File::createDirectory($path);
        $realTableName = "Base";

        $phpNamespace = new PhpNamespace("App\\HttpController");
        $phpNamespace->addUse(AnnotationController::class);
        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->setAbstract(true);
        $phpClass->addExtend(AnnotationController::class);
        $phpClass->addComment("BaseController");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        $statusNameSpace = '\\'.Status::class;
        $phpClass->addMethod('index')
            ->addBody(<<<BODY
             \$this->actionNotFound('index');
BODY
            );

        return $this->createPHPDocument($this->appPath . '/HttpController/' . $realTableName, $phpNamespace);
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
    protected function createPHPDocument($fileName, $fileContent)
    {
        $content = "<?php\n\n{$fileContent}\n";
        $result = file_put_contents($fileName . '.php', $content);

        return $result == false ? $result : $fileName . '.php';
    }
}