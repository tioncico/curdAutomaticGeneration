<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 12:07
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize()->globalInitialize();
$baseController = new \AutomaticGeneration\InitGeneration\BaseController();
$baseController->generate();