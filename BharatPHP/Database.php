<?php

namespace BharatPHP;

use PDO;

/**
 * Description of Database
 *
 * @author Sandeep Kumar
 */
abstract class Database {

    static private $db = [];
    static protected $connection = "mysql";

    /**
     * getDB
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB() {


        $db_config = config("database");

        if (isset(self::$db[static::$connection])) {
            return self::$db[static::$connection];
        }

        if (isset($db_config['connections'][static::$connection])) {
            $dsn = 'mysql:host=' . $db_config['connections'][static::$connection]['host'] . ';dbname=' . $db_config['connections'][static::$connection]['database'] . ';charset=utf8';
            $db = new PDO($dsn, $db_config['connections'][static::$connection]['username'], $db_config['connections'][static::$connection]['password']);
            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8;SET time_zone = '+00:00'");
            self::$db[static::$connection] = $db;
        }
        //TODO:Handle error 



        return self::$db[static::$connection];
    }

}
