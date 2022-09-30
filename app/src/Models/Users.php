<?php

namespace App\Models;

use BharatPHP\Model;

class Users extends Model {

    static protected $table = "users";
    static protected $connection = "mysql";

    public static function getAll() {
        return parent::getAll();
    }

}
