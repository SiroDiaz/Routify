<?php

namespace SimpleRouter;


class Order {
    private $uri;
    private $response;
    private $method;

    public function __construct($uri, $method, $response) {
        $this->uri = $uri;
        $this->method = $method;
        $this->response = $response;
    }

    /**
     * @return mixed
     */

    public function getUri() {
        return $this->uri;
    }

    /**
     * @return mixed
     */

    public function getMethod() {
        return $this->method;
    }

    /**
     * @return mixed
     */

    public function getResponse() {
        return $this->response;
    }
}