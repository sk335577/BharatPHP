<?php

namespace BharatPHP;

use PDO;
use BharatPHP\Database;

abstract class Model extends Database {

    /**
     * getAll
     * 
     * @return array
     */
    public static function getAll() {
        $db = self::getDB();
        $statement = $db->query('SELECT * FROM ' . static::$table);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}
