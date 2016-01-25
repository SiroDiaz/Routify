<?php

namespace SimpleRouter;


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

    private function addOrder($uri, $method, $response) {
        if($this->find($uri, $method)) {
            return false;
        }

        $totalRoutes = count($this->routes);
        $this->routes[$totalRoutes] = new Order($uri, $method, $response);
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
     * @param $uri
     * @param $method
     * @return bool
     */

    public function find($uri, $method) {
        $found = false;
        $counter = 0;

        if(count($this->routes) === 0) {
            return $found;
        }

        while($found === false && $counter < count($this->routes)) {
            if ($this->routes[$counter]->getUri() === $uri && $this->routes[$counter]->getMethod() === $method) {
                $found = true;
            }

            $counter++;
        }

        return $found;
    }

    /**
     * Clear the array of orders(routes) registered.
     */

    public function clear() {
        $this->routes = [];
    }

    /**
     * @param $uri
     * @param $response
     */

    public function get($uri, $response) {
        $this->addOrder($uri, Method::GET, $response);
    }

    /**
     * @param $uri
     * @param $response
     */

    public function post($uri, $response) {
        $this->addOrder($uri, Method::POST, $response);
    }

    /**
     * @param $uri
     * @param $response
     */

    public function put($uri, $response) {
        $this->addOrder($uri, Method::POST, $response);
    }

    /**
     * @param $uri
     * @param $response
     */

    public function delete($uri, $response) {
        $this->addOrder($uri, Method::POST, $response);
    }

    /**
     * @param $func
     */

    public function notFound($func) {
        $this->notFound = $func;
    }

    /**
     * @return mixed|null
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
            $params = $this->routerParser->getParams($this->routes[$counter]->getUri());
            return call_user_func_array($this->routes[$counter]->getResponse(), $params);
        } else {
            return call_user_func($this->notFound);
        }
    }
}