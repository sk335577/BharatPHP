<?php

namespace App\Models;

use BharatPHP\Model;

class Users extends Model {

    static protected $table = "users";
    static protected $connection = "mysql";

    public static function getAllUsers($array = array()) {
        return parent::getAll('select * from ' . self::$table);
    }

    public static function getAllUsersCount($array = array()) {
        return parent::getCount('select count(1) as total_users_count from ' . self::$table);
    }

    public static function getUserByUserID($user_id) {
        return parent::getOne('select * from ' . self::$table . ' WHERE id=:id ', ['id' => 1]);
    }

    public static function createUser($user_data) {
        return parent::insertOrUpdate($user_data);
    }

    public static function updateUserByUserID($user_id, $user_data) {
        return parent::insertOrUpdate($user_data, 'update', ['id' => $user_id]);
    }

}
