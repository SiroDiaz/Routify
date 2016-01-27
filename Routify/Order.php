<?php

namespace Routify;


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

    public function __construct($uri, $method, $response) {
        $this->uri = $uri;
        $this->method = $method;
        $this->response = $response;
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
}