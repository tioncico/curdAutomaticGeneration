<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:15
 */

namespace AutomaticGeneration\ControllerMethodGeneration;


use EasySwoole\ORM\Utility\Schema\Column;

class Delete extends MethodAbstract
{

    protected $methodName = 'delete';
    protected $methodDescription = '删除数据';
    protected $responseParam = [
        'code'   => '状态码',
        'result' => 'api请求结果',
        'msg'    => 'api提示信息',
    ];
    protected $authParam = 'userSession';
    protected $methodAllow = "GET,POST";
    protected $responseSuccessText = '{"code":200,"result":[],"msg":"新增成功"}';
    protected $responseFailText = '{"code":400,"result":[],"msg":"新增失败"}"}';


    function getMethodBody()
    {
        $modelName = $this->getModelName();
        $table = $this->controllerConfig->getTable();

        $this->chunkTableColumn(function (Column $column, string $columnName) use ($table, &$methodBody) {
            $paramValue = $this->newColumnParam($column);
            if ($columnName != $table->getPkFiledName()) {
                return false;
            }
            $paramValue->required = '';
            $this->addColumnComment($paramValue);
            return true;
        });
        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (!\$info) {
    \$this->writeJson(Status::CODE_OK, \$info, "数据不存在.");
}

\$info->destroy();
\$this->writeJson(Status::CODE_OK, [], "删除成功.");
Body;
        return $methodBody;
    }


}