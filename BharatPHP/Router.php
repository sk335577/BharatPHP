<?php

namespace BharatPHP;

use BharatPHP\Exception\NotFoundException;

class Router {

    private Request $request;
    private Response $response;
    private static array $routeMap = [];

    /**
     * __construct
     * 
     * 
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * get
     * 
     * @param string $url
     * @param type $callback
     */
    public static function get(string $url, $callback) {
        self::$routeMap['get'][$url] = $callback;
    }

    public static function post(string $url, $callback) {
        self::$routeMap['post'][$url] = $callback;
    }

    /**
     * @return array
     */
    public function getRouteMap($method): array {
        return self::$routeMap[$method] ?? [];
    }

    public function getCallback() {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');

        // Get all routes for current request method
        $routes = $this->getRouteMap($method);

        $routeParams = false;

        // Start iterating registed routes
        foreach ($routes as $route => $callback) {
            // Trim slashes
            $route = trim($route, '/');
            $routeNames = [];

            if (!$route) {
                continue;
            }

            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {
                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++) {
                    $values[] = $valueMatches[$i][0];
                }
                $routeParams = array_combine($routeNames, $values);

                $this->request->setRouteParams($routeParams);
                return $callback;
            }
        }

        return false;
    }

    public function resolve() {

        $method = $this->request->getMethod();
        $url = $this->request->getUrl();

        $callback = self::$routeMap[$method][$url] ?? false;

        if (!$callback) {
            $callback = $this->getCallback();
            if ($callback === false) {
                throw new NotFoundException();
            }
        }

        if (is_string($callback)) {
            return $this->renderView($callback);
        }


        if (is_array($callback)) {

            $controller = new $callback[0]($this->request, $this->response);
            $controller->action = $callback[1];
            Application::app()->controller = $controller;
            $middlewares = $controller->getMiddlewares();
            foreach ($middlewares as $middleware) {
                $middleware->execute();
            }
            $callback[0] = $controller;
        }
        // return call_user_func($callback, $this->request, $this->response);
        return call_user_func($callback);
    }

    public function renderView($view, $params = []) {
        return Application::app()->view()->renderView($view, $params);
    }

    public function renderViewOnly($view, $params = []) {
        return Application::app()->view()->renderViewOnly($view, $params);
    }

}
