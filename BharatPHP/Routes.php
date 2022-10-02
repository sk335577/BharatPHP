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
