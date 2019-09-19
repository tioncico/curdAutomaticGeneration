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

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\Utility\File;
use EasySwoole\Validate\Validate;
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
        $poolObjectName = $poolObjectName ?? Mysqli::class;
        $realTableName = "BaseModel";

        $phpNamespace = new PhpNamespace("App\\Model");
        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->addComment("BaseModel");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        $phpClass->addProperty('db')->setVisibility('protected');
        $phpClass->addMethod('__construct')
            ->addBody(<<<BODY
    \$this->db = \$dbObject;
BODY
            )
            ->addParameter('dbObject')
            ->setTypeHint($poolObjectName);

        $phpClass->addMethod('getDb')
            ->setReturnType($poolObjectName)
            ->addBody(<<<BODY
             return \$this->db;
BODY
            );
        return $this->createPHPDocument($this->appPath . '/Model/' . $realTableName, $phpNamespace);
    }

    function initBaseController($poolObjectName = null)
    {
        $path = $this->appPath . '/HttpController';
        File::createDirectory($path);
        $realTableName = "Base";

        $phpNamespace = new PhpNamespace("App\\HttpController");
        $phpClass = $phpNamespace->addClass($realTableName);
        $phpClass->setAbstract(true);
        $phpClass->addExtend(Controller::class);
        $phpClass->addComment("BaseController");
        $phpClass->addComment("Class {$realTableName}");
        $phpClass->addComment('Create With Automatic Generator');
        $statusNameSpace = '\\'.Status::class;
        $phpClass->addMethod('index')
            ->addBody(<<<BODY
             \$this->actionNotFound('index');
BODY
            );
        $phpClass->addMethod('onRequest')->setReturnNullable(true)->setReturnType('bool')
        ->setBody(<<<BODY
if (!parent::onRequest(\$action)) {
    return false;
};
/*
* 各个action的参数校验
*/
\$v = \$this->getValidateRule(\$action);
if (\$v && !\$this->validate(\$v)) {
    \$this->writeJson($statusNameSpace::CODE_BAD_REQUEST, ['errorCode' => 1, 'data' => []], \$v->getError()->__toString());
    return false;
}
return true;
BODY
        )->addParameter('action')->setTypeHint('string')->setNullable(true);

        $phpClass->addMethod('getValidateRule')->setAbstract(true)->setVisibility('protected')
            ->setReturnNullable(true)
            ->setReturnType(Validate::class)
            ->addParameter('action')->setTypeHint('string')->setNullable(true);

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