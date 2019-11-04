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