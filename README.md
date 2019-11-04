# curdAutomaticGeneration
curd自动生成代码工具,可自动生成curd全套model,controller

> 2.x版本为easyswoole/orm专用版本


## 创建orm连接,获取到数据表数据
```php
$mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
$connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);

$tableName = 'user_list';
$tableObjectGeneration =  new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
$schemaInfo = $tableObjectGeneration->generationTable();
```


## 初始化项目
可自动生成baseModel和baseController，生成到App目录之下
```php
$init = new \AutomaticGeneration\Init();
$init->initBaseModel();
$init->initBaseController();
```
> BaseModel基于`\EasySwoole\ORM\AbstractModel`,BaseController基于`\EasySwoole\Http\AbstractInterface\AnnotationController`

## 生成model
```php
$path = '\\User';
$modelConfig = new \AutomaticGeneration\Config\ModelConfig();
$modelConfig->setBaseNamespace("App\\Model" . $path);
$modelConfig->setTable($schemaInfo);//传入上面的数据表数据
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
$modelConfig->setTablePre("");
$modelConfig->setExtendClass(\App\Model\BaseModel::class);
$modelConfig->setKeyword('');//生成该表getAll关键字
$modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
$result = $modelBuilder->generateModel();
var_dump($result);
```
> model的配置文件可以自己看源码

## 生成controller
```php
$path = '\\Api\\Admin\\User';
$controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
$controllerConfig->setBaseNamespace("App\\HttpController" . $path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
$controllerConfig->setTablePre('');
$controllerConfig->setTable($schemaInfo);//传入上面所说的数据表数据
$controllerConfig->setExtendClass(\App\HttpController\Base::class);
$controllerConfig->setModelClass($modelBuilder->getClassName());
$controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
$result = $controllerBuilder->generateController();
var_dump($result);
var_dump($result);
```
> 生成控制器的其他配置文件可以看源码，以及依赖model的className

### go函数清除定时器
```php
\EasySwoole\Component\Timer::getInstance()->clearAll();

```


