<?php

class Ajde_Db_Adapter_MySql extends Ajde_Db_Adapter_Abstract
{
    protected $_connection = null;
    protected $_dbname = null;

    public static $_cache = [];

    public function __construct($dsn, $user, $password)
    {
        $dsnString = 'mysql:';
        foreach ($dsn as $k => $v) {
            if ($k === 'dbname') {
                $this->_dbname = $v;
            }
            $dsnString .= $k . '=' . $v . ';';
        }
        parent::__construct(
            $dsnString,
            $user,
            $password,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",    // Modern, please
                PDO::ATTR_EMULATE_PREPARES => true                    // Better caching
            ]
        );
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    public static function getCache($sql)
    {
        if (array_key_exists($sql, self::$_cache)) {
            return self::$_cache[$sql];
        } else {
            return null;
        }
    }

    public static function saveCache($sql, $result)
    {
        self::$_cache[$sql] = $result;

        return $result;
    }

    public function getTableStructure($tableName)
    {
        $sql = 'SHOW FULL COLUMNS FROM ' . $tableName;
        $cache = self::getCache($sql);
        if (isset($cache)) {
            return $cache;
        }
        $statement = $this->getConnection()->query($sql);

        return self::saveCache($sql, $statement->fetchAll());
    }

    public function getForeignKey($childTable, $childColumn)
    {
        $sql = sprintf("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE COLUMN_NAME = %s AND TABLE_NAME = %s AND TABLE_SCHEMA = %s",
            $this->getConnection()->quote($childColumn),
            $this->getConnection()->quote($childTable),
            $this->getConnection()->quote($this->_dbname)
        );
        $cache = self::getCache($sql);
        if (isset($cache)) {
            return $cache;
        }
        $statement = $this->getConnection()->query($sql);

        return self::saveCache($sql, $statement->fetch());
    }

    public function getParents($childTable)
    {
        $sql = sprintf("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = %s AND TABLE_SCHEMA = %s",
            $this->getConnection()->quote($childTable),
            $this->getConnection()->quote($this->_dbname)
        );
        $cache = self::getCache($sql);
        if (isset($cache)) {
            return $cache;
        }
        $statement = $this->getConnection()->query($sql);

        return self::saveCache($sql, $statement->fetchAll());
    }

    public static function getFieldType($type)
    {
        // TODO: Quite naive, rough implementation
        // @see http://dev.mysql.com/doc/refman/5.0/en/data-types.html

        $types = [
            Ajde_Db::FIELD_TYPE_NUMERIC => "tinyint smallint mediumint int bigint decimal float double real bit boolean serial",
            Ajde_Db::FIELD_TYPE_TEXT => "char varchar tinytext mediumtext text longtext binary varbinary tinyblob mediumblob blob longblob",
            Ajde_Db::FIELD_TYPE_DATE => "date datetime timestamp time year",
            Ajde_Db::FIELD_TYPE_ENUM => "enum set",
            Ajde_Db::FIELD_TYPE_SPATIAL => "geometry point linestring polygon multipoint multilinestring multipolygon geometrycollection"
        ];

        // Get normalized type
        //$typeName = Ajde_Db::FIELD_TYPE_STRING;
        $typeName = $type;
        $start = strpos($type, '(');
        $mysqlName = $start > 0 ? trim(substr($type, 0, $start)) : $type;
        foreach ($types as $typeKey => $haystack) {
            if (substr_count($haystack, $mysqlName) > 0) {
                $typeName = $typeKey;
                break;
            }
        }

        // Get length/values
        $length = strpos($type, ')') - $start;
        $typeLength = $start > 0 ? trim(substr($type, $start + 1, $length - 1)) : null;

        // TODO: precision and limits
        return [
            'type' => $typeName,
            'length' => $typeLength
        ];
    }

}
