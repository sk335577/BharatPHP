<?php

namespace BharatPHP;

class Config {

    private static $env_config = array();
    private static $config = array();
    private static $config_cache = array();

    public static function loadEnvConfig($config_path) {

        $lines = file($config_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $env_config = [];

        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            $env_config[$name] = $value;
//            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
//                putenv(sprintf('%s=%s', $name, $value));
//                $_ENV[$name] = $value;
//                $_SERVER[$name] = $value;
//            }
        }
        self::$env_config = ($env_config);
    }

    public static function init($config = array()) {
//        self::$config['app_env_config'] = self::loadEnvConfig();
        self::$config = array_merge(self::$config, $config);
    }

    public static function set($data) {
        self::$config = array_merge(self::$config, $data);
    }

    public static function setConfig($path, $value) {

        $loc = &self::$config;

        foreach (explode('.', $path) as $step) {
            $loc = &$loc[$step];
        }
        return $loc = $value;
    }

    public static function envConfig($config_name = '', $default = '') {
        if (isset(self::$env_config[$config_name])) {
            return self::$env_config[$config_name];
        }
        return $default;
    }

    public static function get($path = '', $default = '') {

//        if (isset(self::$config_cache[$path])) {
//            return self::$config_cache[$path];
//        }

        if (empty($path)) {
            return self::$config;
        }

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
//        self::$config_cache[$path] = $result;

        return $result;
    }

    public static function getAll() {
        return self::$config;
    }

}
