<?php

namespace BharatPHP;

use PDO;

/**
 * Description of Database
 *
 * @author Sandeep Kumar
 */
class Database {

    static private $db = [];
    static protected $connection = "mysql";

    /**
     * getDB
     * Get the PDO database connection
     *
     * @return mixed
     */
    public static function getDBConnection($connection = null) {


        $db_config = config("database");

        if (!is_null($connection)) {
            $db_connection = $connection;
        } else {
            $db_connection = static::$connection;
        }



        if (isset(self::$db[$db_connection])) {
            return self::$db[$db_connection];
        }

        if (isset($db_config['connections'][$db_connection])) {

            $dsn = 'mysql:host=' . $db_config['connections'][$db_connection]['host'] . ';dbname=' . $db_config['connections'][$db_connection]['database'] . ';charset=utf8';
            $db = new PDO($dsn, $db_config['connections'][$db_connection]['username'], $db_config['connections'][$db_connection]['password']);
            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8;SET time_zone = '+00:00'");
            self::$db[$db_connection] = $db;
        }
        //TODO:Handle error 



        return self::$db[$db_connection];
    }

}
