<?php

namespace Routify;


class Router {

    /**
     * @var array The list of routes that contains the callback function and the request method.
     */
    private $routes;

    /**
     * @var string The path requested for the client(browser, cli, app...).
     */
    private $path;

    /**
     * @var string The requested method(GET, POST, PUT, DELETE) used by the client.
     */
    private $requestMethod;

    /**
     * @var object The instance of the router parser.
     */
    private $routerParser;

    /**
     * @var callable The action to be executed if the any route doesn't match
     */
    private $notFound;

    public function __construct() {
        $this->routes = [];
        // only path, without ?, #, etc.
        $this->path = (isset($_SERVER['REQUEST_URI'])) ?
            rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) : '/';
        $this->routerParser = new RouterParser($this->path);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];

        $this->notFound = function() {};
    }

    /**
     * Returns the path.
     *
     * @return string
     */

    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the array of routes.
     *
     * @return array
     */

    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Return the request method used by the client.
     *
     * @return string
     */

    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * Sets the path. Don't use it in production. Only for tests.
     *
     * @param $path
     */

    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Set the request method. Used in tests or development.
     *
     * @param $method
     */

    public function setRequestMethod($method) {
        $this->requestMethod = $method;
    }

    /**
     * Searchs in the array of routes the route that matches(same URI
     * and request method).
     *
     * @param $uri string
     * @param $method string The request method
     * @return bool true if exist a result
     */

    public function find($uri, $method) {
        $found = false;
        $counter = 0;

        if(count($this->routes) === 0) {
            return $found;
        }

        while($found === false && $counter < count($this->routes)) {
            if($this->routes[$counter]->getUri() === $uri && $this->routes[$counter]->getMethod() === $method) {
                $found = true;
            }

            $counter++;
        }

        return $found;
    }

    /**
     * Adds a new route to the routes array.
     *
     * @param $uri
     * @param $method
     * @param $response
     * @return bool false if the route has not been added.
     */

    private function addRoute($uri, $method, $response, array $middleware = []) {
        if($this->find($uri, $method)) {    // search if exists an apparition
            return false;
        }

        $totalRoutes = count($this->routes);
        $this->routes[$totalRoutes] = new Order($uri, $method, $response, $middleware);
        return true;
    }

    /**
     * Clear the array of orders(routes) registered.
     */

    public function clear() {
        // unset($this->routes);
        $this->routes = [];
    }

    /**
     * Register the GET request.
     *
     * @param $uri
     * @param $response
     */

    public function get($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::GET, $response, $middleware);
    }

    /**
     * Register the POST request.
     *
     * @param $uri
     * @param $response
     */

    public function post($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::POST, $response, $middleware);
    }

    /**
     * Register the PUT request.
     *
     * @param $uri
     * @param $response
     */

    public function put($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::POST, $response, $middleware);
    }

    /**
     * Register the DELETE request.
     *
     * @param $uri string The path requested
     * @param $response callable Action to response
     */

    public function delete($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::POST, $response, $middleware);
    }

    /**
     * Register the PATCH request.
     *
     * @param $uri string The path requested
     * @param $response callable Action to response
     */

    public function patch($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::PATCH, $response, $middleware);
    }

    /**
     * Register one or more requests for the same uri.
     *
     * @param $uri string The path requested
     * @param $response callable Action to response
     * @param $methods mixed Methods to bind. Optional. GET by default
     * @param $middleware array Middlewares before and after. Optional
     */

    public function both($uri, $response, $methods = Method::GET, array $middleware = []) {
        if(is_array($methods)) {
            foreach($methods as $method) {
                $this->addRoute($uri, $method, $response, $middleware);
            }
        } else {
            $this->addRoute($uri, $methods, $response, $middleware);
        }
    }

    public function any($uri, $response, array $middleware = []) {
        $this->addRoute($uri, Method::GET, $response, $middleware);
        $this->addRoute($uri, Method::POST, $response, $middleware);
        $this->addRoute($uri, Method::PUT, $response, $middleware);
        $this->addRoute($uri, Method::DELETE, $response, $middleware);
        $this->addRoute($uri, Method::PATCH, $response, $middleware);
    }

    /**
     * Sets a callback for the notFound event.
     *
     * @param $func callable
     */

    public function notFound($func) {
        if(is_callable($func)) {
            $this->notFound = $func;
        }
    }

    /**
     * Initialize the router app and start to run
     * over the array of routes for any appearance.
     * If there is a result then call the callback associate.
     * If there is not a result it will execute the notFound
     * action.
     *
     * @return mixed The result of execute the callback.
     */

    public function run() {
        $found = false;
        $counter = 0;
        while($found === false && $counter < count($this->routes)) {
            if($this->routerParser->match($this->routes[$counter]->getUri()) && $this->routes[$counter]->getMethod() === $this->requestMethod) {
                $found = true;
            } else {
                $counter++;
            }
        }

        if($found) {
            // run the before middleware if it exists
            if($this->routes[$counter]->hasBefore()) {
                call_user_func($this->routes[$counter]->getMiddlewares()['before']);
            }

            $params = $this->routerParser->getParams($this->routes[$counter]->getUri());
            $response = call_user_func_array($this->routes[$counter]->getResponse(), $params);

            // run the after middleware if it exists
            if($this->routes[$counter]->hasAfter()) {
                call_user_func($this->routes[$counter]->getMiddlewares()['after']);
            }

            return $response;
        } else {
            return call_user_func($this->notFound);
        }
    }
}