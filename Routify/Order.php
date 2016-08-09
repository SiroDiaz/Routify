<?php

namespace Routify;

use Routify\Exceptions\InvalidMiddlewareException;
use Routify\Exceptions\MethodException;

class Order {

    /**
     * @var string Name of the route
     */
    private $name;

    /**
     * @var string The path pattern.
     */
    private $uri;

    /**
     * @var callable The callback response.
     */
    private $response;

    /**
     * @var string The request method for the path pattern.
     */
    private $method;

    /**
     * @var array Contains all middlewares associate to the action.
     */
    private $middlewares = [];

    /**
     * @var array The types that are supported(for middlewares).
     */
    private $middlewareTypes = ['before', 'after'];

    /**
     * @var array request methods availables.
     */
    private $availableRequestMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

    public function __construct($uri, $method, $response, array $middlewares = [], $name = '') {
        $this->uri = $uri;
        if(!in_array(mb_strtoupper($method), $this->availableRequestMethods)) {
            throw new MethodException("The request method is invalid");
        }
        $this->method = $method;
        $this->response = $response;
        if($this->isValidMiddleware($middlewares)) {
            $this->middlewares = $middlewares;
        }
        $this->name = $name;
    }

    /**
     * Returns the route name.
     *
     * @return string
     */

    public function getName() {
        return $this->name;
    }

    /**
     * Returns the uri(the pattern path).
     *
     * @return mixed
     */

    public function getUri() {
        return $this->uri;
    }

    /**
     * Returns the request method registered.
     *
     * @return mixed
     */

    public function getMethod() {
        return $this->method;
    }

    /**
     * Returns the callback associated to the pattern.
     *
     * @return mixed
     */

    public function getResponse() {
        return $this->response;
    }

    /**
     * Returns a list of middlewares registered.
     *
     * @return array
     */

    public function getMiddlewares() {
        return $this->middlewares;
    }

    /**
     * Checks if the middleware contains before callback.
     *
     * @return bool
     */

    public function hasBefore() {
        return array_key_exists('before', $this->middlewares);
    }

    /**
     * Checks if the middleware contains after callback.
     *
     * @return bool
     */

    public function hasAfter() {
        return array_key_exists('after', $this->middlewares);
    }

    /**
     * Checks if middlewares passed as an associative array
     * don't contain keys different to "before" and "after".
     *
     * @param array $middleware The associative array containing
     *          middleware callbacks.
     * @return bool
     * @throws InvalidMiddlewareException
     */

    private function isValidMiddleware($middleware) {
        // if there is not middlewares then it is valid
        if(count($middleware) === 0) {
            return true;
        }

        foreach($middleware as $key => $value) {
            if(!in_array($key, $this->middlewareTypes)) {
                throw new InvalidMiddlewareException("Only before and after middleware types are valid");
            }
            if(!is_callable($value)) {
                throw new InvalidMiddlewareException("The middleware must be callable");
            }
        }

        return true;
    }
}