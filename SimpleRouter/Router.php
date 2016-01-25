<?php

namespace SimpleRouter;


class Router {
    /**
     * @var array The list of routes that contains the callback function and the request method
     */
    private $routes;
    /**
     * @var string The before middleware route patterns and their handling functions
     */
    private $path;
    private $requestMethod;
    private $routerParser;
    private $notFound;

    public function __construct() {
        $this->routes = [];
        // only path, without ?, #, etc.
        $this->path = (isset($_SERVER['REQUEST_URI'])) ?
            rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) : '/';
        $this->routerParser = new RouterParser($this->path);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    private function addOrder($uri, $method, $response) {
        if($this->find($uri, $method)) {
            return false;
        }

        $totalRoutes = count($this->routes);
        $this->routes[$totalRoutes] = new Order($uri, $method, $response);
    }

    /**
     * @return string
     */

    public function getPath() {
        return $this->path;
    }

    /**
     * @return array
     */

    public function getRoutes() {
        return $this->routes;
    }

    /**
     * @return string
     */

    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * @param $path
     */

    public function setPath($path) {
        $this->path = $path;
    }

    /**
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
        }

        return null;
    }
}