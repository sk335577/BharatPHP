<?php

namespace BharatPHP;

use PDO;
use BharatPHP\Database;

abstract class Model extends Database {

    public static function execute($query, $array = array()) {
        $db = self::getDBConnection();
        $stmt = $db->prepare($query);
        $ret = $stmt->execute($array);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }

    /**
     * 
     * @param type $query
     * @param type $array
     * @return type
     */
    public static function getCount($query, $array = array()) {
        $stmt = static::execute($query, $array);
        $rows = $stmt->fetch();
        return $rows;
    }

    /**
     * 
     * @param type $query
     * @param type $array
     * @return type
     */
    public static function getAll($query, $array = array()) {
        $stmt = static::execute($query, $array);
        $rows = $stmt->fetchAll();
        return $rows;
    }

    /**
     * 
     * @param type $query
     * @param type $array
     * @return array
     */
    public static function getOne($query, $array = array()) {
        $stmt = static::execute($query, $array);
        $row = $stmt->fetch();
        return $row;
    }

    /**
     * 
     * @param type $data
     * @param type $action
     * @param type $conditions
     * @return type
     */
    public static function insertOrUpdate($data, $action = 'insert', $conditions = '') {

        reset($data);

        $insertcolumns = $insertvalues = $updatevalues = $duplicatevalues = '';
        foreach ($data as $idx => $d) {


            switch (strtolower((string) $d)) {
                case 'now()':
                case 'null':
                    $insertcolumns .= "$idx, ";
                    $insertvalues .= "$d, ";
                    $updatevalues .= "$idx=$d, ";
                    break;
                default:
                    $insertcolumns .= "$idx, ";
                    $insertvalues .= ":$idx, ";
                    $duplicatevalues .= "$idx=:dup_$idx, ";
                    $updatevalues .= "$idx=:$idx, ";
                    if (is_null($d))
                        $d = '';
                    $insertarray[$idx] = $d;
                    $duplicatearray['dup_' . $idx] = $d;
                    break;
            }
        }

        $insertcolumns = rtrim($insertcolumns, ', ');
        $insertvalues = rtrim($insertvalues, ', ');
        $duplicatevalues = rtrim($duplicatevalues, ', ');
        $updatevalues = rtrim($updatevalues, ', ');

        $action = strtolower($action);
        $delayed = '';

        if (substr_count($action, 'delayed') > 0) {
            $delayed = 'DELAYED';
        }

        if (substr_count($action, 'insert') > 0) {

            $query = "INSERT $delayed INTO " . static::$table . " ($insertcolumns) VALUES ($insertvalues)";

            if (substr_count($action, 'duplicate') > 0) {
                $query .= " ON DUPLICATE KEY UPDATE $duplicatevalues";
                $insertarray = array_merge($insertarray, $duplicatearray);
            }
        } elseif (substr_count($action, 'update') > 0) {

            $operators = array('=', '<', '>', '<>', '>=', '<=', 'like');

            foreach ($conditions as $idx => $parameter) {
                $where_parts = explode(' ', $idx);

                $where_var = trim($where_parts[0]);

                $where_operator = '=';

                if (isset($where_parts[1])) {
                    $where_operator = trim($where_operator[1]);
                }

                if (!in_array($where_operator, $operators)) {
                    continue;
                }

                if ($where_operator == 'like') {
                    $parameter = "%$parameter%";
                }

                $where_array[] = "$where_var $where_operator :where_$idx";
                $insertarray['where_' . $idx] = $parameter;
            }
            $where = implode(' AND ', $where_array);
            $query = "UPDATE " . static::$table . " SET $updatevalues WHERE $where";
        }
        return static::execute($query, $insertarray);
    }

    public static function getastInsertId() {
        return self::getDBConnection()->lastInsertId();
    }

}
