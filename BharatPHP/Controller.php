<?php

namespace BharatPHP;

use BharatPHP\Application;
use BharatPHP\View;
use BharatPHP\Config;
use BharatPHP\Request;

abstract class Controller {

    protected $params;
    protected $app;
    protected $view;
    protected $request;
    protected $response;
    public string $action = '';
    protected array $middlewares = [];

    public function __construct(Request $request, Response $response) {
//        $this->view = new View(Config::get('paths.views'));
        // $this->app = $app;
        $this->request = $request;
        $this->response = $response;
    }

    public function view($view, $params = []): string {
        return Application::app()->view()->renderView($view, $params);
    }

    public function registerMiddleware($middleware) {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    public function request() {
        return $this->request;
    }

    public function response() {
        return $this->response;
    }

    public function services($service) {
        return $this->app->services()->get($service);
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }

    public function __call($name, $args) {
        // $method = $name . 'Action';
        $method = $name . '';
        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array(array($this, $method), $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before() {
        
    }

    protected function after() {
        
    }

}
