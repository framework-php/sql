<?php

namespace GuilhermeHideki\Database\SQL;

use InvalidArgumentException;
use PDO;

/**
 * PDO Factory
 *
 * @package GuilhermeHideki\Database\SQL
 */
class PdoFactory
{
    /**
     * PDO Options
     *
     * @var array
     */
    public static $pdoOptions = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    /**
     * @var array[]
     */
    public $configs;

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param string $type
     *
     * @return PDO
     * @throws InvalidArgumentException
     */
    public function build($type)
    {
        return static::getPdo($this->configs[$type]);
    }

    public static function getPdo($data)
    {
        return new PDO(static::mysqlConnectionString(
            $data['host'], $data['port'], $data['database']),
            $data['user'], $data['password'], static::$pdoOptions);
    }

    /**
     * Returns the MySQL connecion string.
     *
     * @param string $host     database host name.
     * @param string $database database name.
     * @param string $port     database port (default 3306).
     *
     * @return string DSN
     */
    private static function mysqlConnectionString($host, $database, $port=3306)
    {
        return "mysql:host=$host;port=$port;dbname=$database;charset=utf8";
    }

}