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
use App\Utility\Pool\MysqlPoolObject;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Utility\File;
use Nette\PhpGenerator\PhpNamespace;

class init
{
    protected $appPath;

    public function __construct($appPath = 'Application')
    {
        defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', dirname(__FILE__, 2));
        require_once EASYSWOOLE_ROOT . '/EasySwooleEvent.php';
        \EasySwoole\EasySwoole\Core::getInstance()->initialize();
        $this->appPath = EASYSWOOLE_ROOT . '/' . $appPath;
    }

    function initBaseModel($poolObjectName = null)
    {
        $path = $this->appPath . '/Model';
        File::createDirectory($path);
        $poolObjectName = $poolObjectName ?? MysqlPoolObject::class;
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
        $phpClass->addMethod('getDbConnection')
            ->setReturnType($poolObjectName)
            ->addBody(<<<BODY
             return \$this->db;
BODY
            );
        return $this->createPHPDocument($this->appPath . '/Model/' . $realTableName, $phpNamespace);
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
        if (file_exists($fileName . '.php')) {
            echo __CLASS__ . "当前路径已经存在文件,是否覆盖?(y/n)\n";
            if (trim(fgets(STDIN)) == 'n') {
                echo "已结束运行";
                return false;
            }
        }
        $content = "<?php\n\n{$fileContent}\n";
        $result = file_put_contents($fileName . '.php', $content);

        return $result == false ? $result : $fileName . '.php';
    }

}