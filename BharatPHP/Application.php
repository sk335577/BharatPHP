<?php

namespace BharatPHP;

use BharatPHP\Router;
use BharatPHP\Services;
use BharatPHP\Events;
use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;

class Application {

    private static Application $app;
    private Router $router;
    private Services $services;
    private Request $request;
    private Response $response;
    private Events $events;
    private View $view;

    public function __construct($config = array()) {

        self::$app = $this;

        Config::init($config); //Save config in config class        

        $this->registerRequest(new Request());

        $this->registerResponse(new Response());

        $this->registerView(new View());

        $this->registerServices(new Services());

        $this->registerRouter(new Router($this->request, $this->response));

        $this->registerEvents(new Events());

        // Load services 
        if (isset($config['services'])) {
            foreach ($config['services'] as $name => $service) {
                $this->services->set($name, $service);
            }
        }

        //Load events 
        if (isset($config['events'])) {
            foreach ($config['events'] as $event) {
                if (isset($event['name']) && isset($event['action'])) {
                    $this->events->on($event['name'], $event['action'], ((isset($event['priority'])) ? $event['priority'] : 0));
                }
            }
        }
    }

    public function registerRequest(Request $request) {
        $this->request = $request;
    }

    public function registerResponse(Response $response) {
        $this->response = $response;
    }

    public function registerRouter(Router $router) {
        $this->router = $router;
    }

    public function registerView(View $view) {
        $this->view = $view;
    }

    public function registerServices(Services $services) {
        $this->services = $services;
    }

    public function registerEvents(Events $events) {
        $this->events = $events;
    }

    public function services() {
        return $this->services;
    }

    public function router() {
        return $this->router;
    }

    public function request() {
        return $this->request;
    }

    public function view() {
        return $this->view;
    }

    public function events() {
        return $this->events;
    }

    /**
     * 
     * @return Application
     */
    public static function app(): Application {
        return self::$app;
    }

    /**
     * 
     */
    public function run() {

        $this->events->trigger('before.app.route', array('app' => $this));

        try {
            $this->router->resolve();
        } catch (\Exception $e) {

            //TODO: Use Response Class
            echo $this->router->renderView('errors/404', [
                'exception' => $e,
            ]);
        }

        $this->events->trigger('after.app.route', array('app' => $this));
    }

}
