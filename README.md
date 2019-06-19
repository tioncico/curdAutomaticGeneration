# curdAutomaticGeneration
curd自动生成代码工具,可自动生成curd全套bean,model,controller

> 本项目支持自行创建mysql连接池管理，也可使用easyswoole mysql-pool组件 https://www.easyswoole.com/Manual/3.x/Cn/_book/Components/CoroutinePool/mysql_pool.html

## 获取数据表内容(通过mysql-pool方式，需要自己注册mysql-pool)
```php
$mysqlConfig = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
\EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',$mysqlConfig);
$db = \EasySwoole\MysqliPool\Mysql::defer('mysql');

$mysqlTable = new \AutomaticGeneration\MysqlTable($db, \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.database'));
$tableName = 'user_list';
$tableColumns = $mysqlTable->getColumnList($tableName);
$tableComment = $mysqlTable->getComment($tableName);
```
## 初始化项目
可自动生成baseModel和baseController，生成到App目录之下
```php
$init = new \AutomaticGeneration\Init();
$init->initBaseModel();
$init->initBaseController();
```


## 生成bean
```php
$path = 'User';

$beanConfig = new \AutomaticGeneration\Config\BeanConfig();
$beanConfig->setBaseNamespace("App\\Model\\".$path);
//    $beanConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
$beanConfig->setTablePre('');
$beanConfig->setTableName('user_list');
$beanConfig->setTableComment($tableComment);
$beanConfig->setTableColumns($tableColumns);
$beanBuilder = new \AutomaticGeneration\BeanBuilder($beanConfig);
$result = $beanBuilder->generateBean();
var_dump(\App\Model\User\UserBean::class);

```
> bean的配置文件可以自己看源码

## 生成model
```php
$path = 'User';
$modelConfig = new \AutomaticGeneration\Config\ModelConfig();
$modelConfig->setBaseNamespace("App\\Model\\".$path);
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
$modelConfig->setTablePre("");
$modelConfig->setExtendClass(\App\Model\BaseModel::class);
$modelConfig->setTableName("user_list");
$modelConfig->setTableComment($tableComment);
$modelConfig->setTableColumns($tableColumns);
$modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
$result = $modelBuilder->generateModel();
var_dump($result);

```
> model的配置文件可以自己看源码

## 生成controller
```php
$path='Api\\Admin\\User';
$controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
$controllerConfig->setBaseNamespace("App\\HttpController\\".$path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
$controllerConfig->setTablePre('');
$controllerConfig->setTableName('user_list');
$controllerConfig->setTableComment($tableComment);
$controllerConfig->setTableColumns($tableColumns);
$controllerConfig->setExtendClass("App\\HttpController\\".$path."\\Base");
$controllerConfig->setModelClass($modelBuilder->getClassName());
$controllerConfig->setBeanClass($beanBuilder->getClassName());
$controllerConfig->setMysqlPoolClass(EasySwoole\MysqliPool\Mysql::class);
$controllerConfig->setMysqlPoolName('test');
$controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
$result = $controllerBuilder->generateController();
```
> 生成控制器的其他配置文件可以看源码，以及依赖model和bean的className


