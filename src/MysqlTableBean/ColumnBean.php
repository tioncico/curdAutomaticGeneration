<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-6-12
 * Time: 下午8:24
 */

namespace AutomaticGeneration\MysqlTableBean;


use EasySwoole\Spl\SplBean;

class ColumnBean extends SplBean
{

    protected $columnName;
    protected $columnType;
    protected $typeLength;
    protected $typeLengthPoint;
    protected $notNull = false;
    protected $default;
    protected $comment;
    protected $ai = false;
    protected $unsigned = false;
    protected $zeroFill = false;
    protected $primary = false;

    const TYPE_NUM_TINYINT = 'TINYINT';
    const TYPE_NUM_SMALLINT = 'SMALLINT';
    const TYPE_NUM_MEDIUMINT = 'MEDIUMINT';
    const TYPE_NUM_INT = 'INT';
    const TYPE_NUM_INTEGER = 'INTEGER';
    const TYPE_NUM_BIGINT = 'BIGINT';
    const TYPE_NUM_FLOAT = 'FLOAT';
    const TYPE_NUM_DOUBLE = 'DOUBLE';
    const TYPE_NUM_DECIMAL = 'DECIMAL';

    const TYPE_TIME_DATE = 'DATE';
    const TYPE_TIME_TIME = 'TIME';
    const TYPE_TIME_YEAR = 'YEAR';
    const TYPE_TIME_DATETIME = 'DATETIME';
    const TYPE_TIME_TIMESTAMP = 'TIMESTAMP';

    const TYPE_CHAR_CHAR = 'CHAR';
    const TYPE_CHAR_VARCHAR = 'VARCHAR';
    const TYPE_CHAR_TINYBLOB = 'TINYBLOB';
    const TYPE_CHAR_TINYTEXT = 'TINYTEXT';
    const TYPE_CHAR_BLOB = 'BLOB';
    const TYPE_CHAR_TEXT = 'TEXT';
    const TYPE_CHAR_MEDIUMBLOB = 'MEDIUMBLOB';
    const TYPE_CHAR_MEDIUMTEXT = 'MEDIUMTEXT';
    const TYPE_CHAR_LONGBLOB = 'LONGBLOB';
    const TYPE_CHAR_LONGTEXT = 'LONGTEXT';

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @param mixed $columnName
     */
    public function setColumnName($columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * @return mixed
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * @param mixed $columnType
     */
    public function setColumnType($columnType): void
    {
        $this->columnType = $columnType;
    }

    /**
     * @return mixed
     */
    public function getTypeLength()
    {
        return $this->typeLength;
    }

    /**
     * @param mixed $typeLength
     */
    public function setTypeLength($typeLength): void
    {
        $this->typeLength = $typeLength;
    }

    /**
     * @return mixed
     */
    public function getTypeLengthPoint()
    {
        return $this->typeLengthPoint;
    }

    /**
     * @param mixed $typeLengthPoint
     */
    public function setTypeLengthPoint($typeLengthPoint): void
    {
        $this->typeLengthPoint = $typeLengthPoint;
    }

    /**
     * @return bool
     */
    public function isNotNull(): bool
    {
        return $this->notNull;
    }

    /**
     * @param bool $notNull
     */
    public function setNotNull(bool $notNull): void
    {
        $this->notNull = $notNull;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return bool
     */
    public function isAi(): bool
    {
        return $this->ai;
    }

    /**
     * @param bool $ai
     */
    public function setAi(bool $ai): void
    {
        $this->ai = $ai;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @param bool $unsigned
     */
    public function setUnsigned(bool $unsigned): void
    {
        $this->unsigned = $unsigned;
    }

    /**
     * @return bool
     */
    public function isZeroFill(): bool
    {
        return $this->zeroFill;
    }

    /**
     * @param bool $zeroFill
     */
    public function setZeroFill(bool $zeroFill): void
    {
        $this->zeroFill = $zeroFill;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @param bool $primary
     */
    public function setPrimary(bool $primary): void
    {
        $this->primary = $primary;
    }


}