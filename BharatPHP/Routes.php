<?php

namespace BharatPHP;

use BharatPHP\Exception\NotFoundException;

class Routes {

    private array $routeMap = [];
    private static $_instance = null;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public static function routes($route_type) {
        
    }

    public static function routeNameToURL($route_name, $route_params = []) {
        $route_name = trim($route_name);

        $r = (self::getInstance()->routeMap);
        foreach ($r as $route_type => $routes) {
            foreach ($routes as $route_url => $route_info) {

                if (isset($route_info['options']) && isset($route_info['options']['name']) && trim($route_info['options']['name']) == $route_name) {

                    $r = appUrl() . ($route_url);
                    foreach ($route_params as $route_param_k => $route_param_v) {
                        $r = str_ireplace('{' . $route_param_k . '}', $route_param_v, $r);
                    }

                    return $r;
                }
            }
        }
        return "/";
    }

    /**
     * get
     * 
     * @param string $url
     * @param type $callback
     */
    public static function get(string $url, $callback, $options = []) {
//        self::getInstance()->routeMap['get'][$url] = $callback;
        self::getInstance()->routeMap['get'][$url] = ['callback' => $callback, 'options' => $options];
        return self::getInstance();
    }

    public static function post(string $url, $callback, $options = []) {
//        self::getInstance()->routeMap['post'][$url] = $callback;
        self::getInstance()->routeMap['post'][$url] = ['callback' => $callback, 'options' => $options];
        return self::getInstance();
    }

//    public static function middleware(string $url = '') {
////        self::$routeMap['middleware'][$url] = $callback;
//    }

    public static function getRouteMap($method): array {
        return self::getInstance()->routeMap[$method] ?? [];
    }

}
