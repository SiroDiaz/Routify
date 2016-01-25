<?php

class OrderTest extends PHPUnit_Framework_TestCase {
    private $order;

    public function __construct() {
        parent::__construct();
        $this->order = new SimpleRouter\Order('/', SimpleRouter\Method::GET, function() { return 'ok, this works'; });
    }

    public function testGetUri() {
        $this->assertEquals($this->order->getUri(), '/');
    }

    public function testGetMethod() {
        $this->assertEquals($this->order->getMethod(), 'GET');
    }

    public function testGetResponse() {
        $response = $this->order->getResponse();
        $this->assertEquals(call_user_func($response), 'ok, this works');
    }
} 