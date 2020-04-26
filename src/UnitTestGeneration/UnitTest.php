<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-26
 * Time: 下午8:45
 */

namespace AutomaticGeneration\UnitTestGeneration;


use AutomaticGeneration\GenerationBase;

class UnitTest extends GenerationBase
{
    public function __construct()
    {
    }

    function getClassName()
    {
        $className = $this->config->getRealTableName() . 'Test';
        return $className;
    }

    function addClassData()
    {

    }

    protected function addTestAddMethod(){


    }



}