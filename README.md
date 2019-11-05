# curdAutomaticGeneration
curd自动生成代码工具,可自动生成curd全套model,controller

> 2.x版本为easyswoole/orm专用版本
## 安装
```
composer require tioncico/curd-automatic-generation 2.x-dev
```  

## easyswoole命令行生成
### 注册ORM数据连接
在`EasySwooleEvent.php`的`initialize`事件中注入orm数据连接池:
```php
public static function initialize()
{
    // TODO: Implement initialize() method.
    date_default_timezone_set('Asia/Shanghai');
    $configData = Config::getInstance()->getConf('MYSQL');
    $config = new \EasySwoole\ORM\Db\Config($configData);
    DbManager::getInstance()->addConnection(new Connection($config));

}
```
### 注入自定义命令
在`/bootstrap.php`中,引入自定义命令:
```php
\EasySwoole\EasySwoole\Command\CommandContainer::getInstance()->set(new \AutomaticGeneration\Generation());
```

### 命令行生成:
生成格式为:
```
 php easyswoole generation 表名 Model命名空间路径 控制器命名空间路径

```
```
 php easyswoole generation article_list Article Api\\Admin 
```

## 自定义生成方式

### 创建orm连接,获取到数据表数据
```php
$mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
$connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);

$tableName = 'user_list';
$tableObjectGeneration =  new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
$schemaInfo = $tableObjectGeneration->generationTable();
```


### 初始化项目
可自动生成baseModel和baseController，生成到App目录之下
```php
$init = new \AutomaticGeneration\Init();
$init->initBaseModel();
$init->initBaseController();
```
> BaseModel基于`\EasySwoole\ORM\AbstractModel`,BaseController基于`\EasySwoole\Http\AbstractInterface\AnnotationController`

### 生成model
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

### 生成controller
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


