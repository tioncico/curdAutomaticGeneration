<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/11/5 0005
 * Time: 10:53
 */

namespace AutomaticGeneration;


use AutomaticGeneration\Config\ControllerConfig;
use AutomaticGeneration\Config\ModelConfig;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\ORM\DbManager;

class Generation implements CommandInterface
{
    public function commandName(): string
    {
        return "Generation";
        // TODO: Implement commandName() method.
    }

    public function exec(array $args): ?string
    {
        go(function () use ($args) {
            $tableName = array_shift($args);
            $modelPath = array_shift($args);
            $controllerPath = array_shift($args);

            $connection = DbManager::getInstance()->getConnection();
            $tableObjectGeneration = new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
            $schemaInfo = $tableObjectGeneration->generationTable();


            $path = $modelPath;
            $modelConfig = new ModelConfig();
            $modelConfig->setBaseNamespace("App\\Model\\" . $path);
            $modelConfig->setTable($schemaInfo);
            $modelConfig->setTablePre("");
            $modelConfig->setExtendClass(\App\Model\BaseModel::class);
            $modelConfig->setKeyword('');//生成该表getAll关键字
            $modelBuilder = new ModelBuilder($modelConfig);
            $result = $modelBuilder->generateModel();
            echo "\e[32m {$result}  generation success \e[0m \n";

            $path = $controllerPath;
            $controllerConfig = new ControllerConfig();
            $controllerConfig->setBaseNamespace("App\\HttpController\\" . $path);
            $controllerConfig->setTablePre('');
            $controllerConfig->setTable($schemaInfo);
            $controllerConfig->setExtendClass(\App\HttpController\Base::class);
            $controllerConfig->setModelClass($modelBuilder->getClassName());
            $controllerBuilder = new ControllerBuilder($controllerConfig);
            $result = $controllerBuilder->generateController();
            echo "\e[32m{$result}  generation success \e[0m \n";
            \EasySwoole\Component\Timer::getInstance()->clearAll();
        });
        return null;
    }

    public function help(array $args): ?string
    {
        // TODO: Implement help() method.
        $logo = 'tioncico/curd-automatic-generation' . PHP_EOL;
        return $logo . <<<HELP_RELOAD
\e[33mOperation:\e[0m
\e[31m  php easyswoole generation tableName modelPath controllerPath   \e[0m
\e[33mIntro:\e[0m
\e[36m  you can generation easyswoole curd code. \e[0m
HELP_RELOAD;
    }


}