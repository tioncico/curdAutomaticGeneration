<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:15
 */

namespace AutomaticGeneration\ControllerMethodGeneration;


use EasySwoole\ORM\Utility\Schema\Column;

class Add extends MethodAbstract
{

    protected $methodName = 'add';
    protected $methodDescription = '新增数据';
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
        $methodBody = <<<Body
\$param = \$this->request()->getRequestParam();
\$data = [

Body;
        $this->chunkTableColumn(function (Column $column, string $columnName) use (&$methodBody) {
            $paramValue = $this->newColumnParam($column);
            if ($column->isNotNull()) {
                $paramValue->required = '';
                $methodBody .= "    '{$columnName}'=>\$param['{$columnName}'],\n";
            } else {
                $paramValue->optional = '';
                $methodBody .= "    '{$columnName}'=>\$param['{$columnName}'] ?? '',\n";
            }
            $this->addColumnComment($paramValue);
        });

        $methodBody .= <<<Body
];
\$model = new {$modelName}(\$data);
\$model->save();
\$this->writeJson(Status::CODE_OK, \$model->toArray(), "新增成功");

Body;
        return $methodBody;
    }


}