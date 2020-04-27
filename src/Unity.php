<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-11
 * Time: 下午9:05
 */

namespace AutomaticGeneration;


use EasySwoole\ORM\Utility\Schema\Table;

class Unity
{
    static function getNamespacePath($namespace)
    {
        $composerJson = json_decode(file_get_contents(EASYSWOOLE_ROOT . '/composer.json'), true);
        return $composerJson['autoload']['psr-4']["{$namespace}\\"] ?? $composerJson['autoload-dev']['psr-4']["{$namespace}\\"];
    }

    /**
     * convertDbTypeToDocType
     * @param $fieldType
     * @return string
     * @author Tioncico
     * Time: 19:49
     */
    static function convertDbTypeToDocType($fieldType)
    {
        if (in_array($fieldType, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {
            $newFieldType = 'int';
        } elseif (in_array($fieldType, ['float', 'double', 'real', 'decimal', 'numeric'])) {
            $newFieldType = 'float';
        } elseif (in_array($fieldType, ['char', 'varchar', 'text'])) {
            $newFieldType = 'string';
        } else {
            $newFieldType = 'mixed';
        }
        return $newFieldType;
    }

    static function chunkTableColumn(Table $table,callable $callback)
    {
        foreach ($table->getColumns() as $column) {
            $columnName = $column->getColumnName();
            $result = $callback($column, $columnName);
            if ($result === true) {
                break;
            }
        }
    }
    static function getModelName($modelClass)
    {
        var_dump($modelClass);
        $modelNameArr = (explode('\\', $modelClass));
        $modelName = end($modelNameArr);
        return $modelName;
    }
}