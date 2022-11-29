<?php

namespace BharatPHP;

class Config {

    private static $config = array();
    private static $config_cache = array();

    public static function init($config = array()) {
        self::$config = $config;
    }

    public static function set($data) {
        self::$config = array_merge(self::$config, $data);
    }

    public static function get($path, $default = '') {

//        if (isset(self::$config_cache[$path])) {
//            return self::$config_cache[$path];
//        }

        $paths = explode('.', $path);

        $result = [];
        foreach ($paths as $path) {
            if (empty($result)) {

                if (isset(self::$config[$path])) {
                    $result = self::$config[$path];
                }
            } else {
                $result = $result[$path];
            }
        }

        if (empty($result)) {
            return $default;
        }
        self::$config_cache[$path] = $result;

        return $result;
    }

    public static function getAll() {
        return self::$config;
    }

}
