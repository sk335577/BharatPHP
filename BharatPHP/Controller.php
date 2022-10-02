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
        $this->request = $request;
        $this->response = $response;
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

}
