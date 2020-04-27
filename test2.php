<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 12:07
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize()->globalInitialize();
$generate = new \AutomaticGeneration\InitGeneration\BaseController();
$generate->generate();
$generate = new \AutomaticGeneration\InitGeneration\BaseUnitTest();
$generate->generate();
$generate = new \AutomaticGeneration\InitGeneration\BaseModel();
$generate->generate();