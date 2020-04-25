<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:15
 */

namespace AutomaticGeneration\ControllerMethodGeneration;


use EasySwoole\ORM\Utility\Schema\Column;

class Update extends MethodAbstract
{

    protected $methodName = 'update';
    protected $methodDescription = '更新数据';
    protected $responseParam = [
        'code'   => '状态码',
        'result' => 'api请求结果',
        'msg'    => 'api提示信息',
    ];
    protected $authParam = 'userSession';
    protected $methodAllow = "GET,POST";
    protected $responseSuccessText = '{"code":200,"result":[],"msg":"更新成功"}';
    protected $responseFailText = '{"code":400,"result":[],"msg":"更新失败"}"}';


    function getMethodBody()
    {
        $modelName = $this->getModelName();
        $table = $this->controllerConfig->getTable();

        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (empty(\$info)) {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
    return false;
}
\$updateData = [];
\n
Body;
        $this->chunkTableColumn(function (Column $column, string $columnName) use ($table,&$methodBody) {
            $paramValue = $this->newColumnParam($column);
            if ($columnName == $table->getPkFiledName()) {
                $paramValue->required = '';
            }else{
                $paramValue->optional = '';
            }
            $methodBody .= "\$updateData['{$columnName}']=\$param['{$columnName}'] ?? \$info->{$columnName};\n";
            $this->addColumnComment($paramValue);
        });

        $methodBody .= <<<Body
\$info->update(\$updateData);
\$this->writeJson(Status::CODE_OK, \$info, "更新数据成功");

Body;
        return $methodBody;
    }


}