<?php

namespace BharatPHP;

use BharatPHP\Router;

class Translator {

    public static $lang = null;
    public static $translations = array();

    public static function load($config, Router $router) {

        $route_params = $router->getParams();

        if (isset($route_params['lang'])) {
            self::$lang = $route_params['lang'];
        } else {
            self::$lang = $config['languages']['default'];
        }
        self::$translations = include_once $config['languages']['path'] . '/' . self::$lang . '.php';
    }

    public static function t($string, $default = '') {
        if (isset(self::$translations[$string])) {
            return self::$translations[$string];
        } else {
            if (!is_null($default)) {
                return $default;
            } else {
                return $string;
            }
        }
    }

}
