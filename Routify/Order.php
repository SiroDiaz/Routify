<?php

namespace Routify;

use Routify\Exceptions\InvalidMiddlewareException;

class Order {

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
     * @var array
     */
    private $middlewareTypes = ['before', 'after'];

    public function __construct($uri, $method, $response, array $middlewares = []) {
        $this->uri = $uri;
        $this->method = $method;
        $this->response = $response;
        // must check if middlewares are valid
        $this->middlewares = $middlewares;
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
     * @return bool
     * @throws InvalidMiddlewareException
     */

    private function isValidMiddleware() {
        foreach($this->middlewares as $key => $value) {
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