<?php

namespace BharatPHP;

use BharatPHP\Exception\NotFoundException;
use BharatPHP\Routes;
use BharatPHP\Translator;

class Router
{

    private Request $request;
    private Response $response;

    /**
     * __construct
     * 
     * 
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {

        //Load routes
        include BharatPHP_ROOT_PATH . '/app/routes/web.php';

        $this->request = $request;
        $this->response = $response;
    }

    public function getCallback()
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');

        // Get all routes for current request method
        $routes = Routes::getRouteMap($method);

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
            //            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
            if (preg_match_all('/\{([a-zA-Z-_0-9.]+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            //            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";
            $routeRegex = "@^" . preg_replace_callback('/\{[a-zA-Z-_0-9.]+(:([^}]+))?}/', fn ($m) => isset($m[2]) ? "({$m[2]})" : '([a-zA-Z-_0-9.]+)', $route) . "$@";

            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {


                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++) {
                    $values[] = $valueMatches[$i][0];
                }
                $routeParams = array_combine($routeNames, $values);

                $this->request->setRouteParams($routeParams);
                //                return $callback['callback'];
                return $callback;
            }
        }

        return false;
    }

    public function resolve()
    {

        $method = $this->request->getMethod();
        $url = $this->request->getUrl();

        $method_routes = Routes::getRouteMap($method);

        $current_route_info = $method_routes[$url] ?? false;

        $callback = isset($current_route_info['callback']) ? $current_route_info['callback'] : false;
        $route_options = isset($current_route_info['options']) ? $current_route_info['options'] : false;

        if (!$callback) {
            $callback_data = $this->getCallback();

            if (isset($callback_data['callback'])) {
                $callback = $callback_data['callback'];
            } else {
                $callback = false;
            }


            if (isset($callback_data['options'])) {
                $route_options = $callback_data['options'];
            }

            if ($callback === false) {

                Translator::init();

                if (config('views.404')) {

                    $error_404_view = config('views.404');

                    //                    return $this->renderView($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']);
                    app()->response()->setCode(404);
                    return app()->response()->setBody(view($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']));
                    //                    return view($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']);
                } else {
                    throw new NotFoundException();
                }
            }
        }


        Translator::init();

        if (is_string($callback)) {
            return $this->renderView($callback);
        }


        if (is_array($callback)) {

            $controller = new $callback[0]($this->request, $this->response);
            $controller->action = $callback[1];

            // app()->controller = $controller;//commented to fix : Cannot access private property BharatPHP\Application::$controller

            if (isset($route_options['middleware'])) {
                //                $middlewares = $controller->getMiddlewares();
                $middlewares = $route_options['middleware'];
                $middleware_response_set = false;

                foreach ($middlewares as $middleware) {
                    $middleware = new $middleware;
                    $middleware_response = $middleware->execute($this->request, $this->response);
                    if (!is_null($middleware_response)) {
                        $middleware_response_set = true;
                    }
                }


                if ($middleware_response_set) {
                    return;
                }
            }



            $callback[0] = $controller;
        }
        // return call_user_func($callback, $this->request, $this->response);
        return call_user_func($callback);
    }

    public function renderView($view, $params = [])
    {
        //        return view()->renderView($view, $params);
        return view($view, $params);
    }

    public function renderViewOnly($view, $params = [])
    {
        return viewWithoutLayout($view, $params);
    }
}
