<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-11
 * Time: 下午9:05
 */

namespace AutomaticGeneration;


class AppLogic
{
    static function getAppPath(){
        $composerJson = json_decode(file_get_contents(EASYSWOOLE_ROOT.'/composer.json'),true);
        return $composerJson['autoload']['psr-4']['App\\'];
    }
}