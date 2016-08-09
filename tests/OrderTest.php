<?php

use Routify\Order;
use Routify\Exceptions\MethodException;
use Routify\Exceptions\InvalidMiddlewareException;

class OrderTest extends PHPUnit_Framework_TestCase {
    private $order;

    public function __construct() {
        parent::__construct();
        $this->order = new Routify\Order('/', Routify\Method::GET, function() { return 'ok, this works'; });
    }

    public function testMethodException() {
        try {
            $router = new Order('/', 'METHOD', function() { return 1; });
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof MethodException);
        }
    }

    public function testInvalidMiddlewareException() {
        try {
            $router = new Order(
                '/',
                'GET',
                function() { return 1; },
                ['initialize' => function() { return 0; }]
            );
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof InvalidMiddlewareException);
        }

        try {
            $router = new Order(
                '/',
                'GET',
                function() { return 1; },
                ['before' => 1]
            );
        } catch(\Exception $e) {
            $this->assertTrue($e instanceof InvalidMiddlewareException);
        }
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
    
    public function testGetMiddlewares() {
        $middlewares = [
            'before' => function() { return 'middleware1';},
            'after' => function() { return 'middleware2';}
        ];

        $order = new Routify\Order(
            '/route/for/something',
            Routify\Method::POST,
            function() { return 'ok'; },
            $middlewares
        );

        $this->assertSame($middlewares, $order->getMiddlewares());
        $this->assertEquals(2, count($order->getMiddlewares()));
    }

    public function testHasBefore() {
        $middlewares = [
            'before' => function() { return 'middleware1';},
            'after' => function() { return 'middleware2';}
        ];

        $order = new Routify\Order(
            '/route/for/something',
            Routify\Method::POST,
            function() { return 'ok'; },
            $middlewares
        );

        $this->assertTrue($order->hasBefore());
    }

    public function testHasAfter() {
        $middlewares = [
            'before' => function() { return 'middleware1';},
            'after' => function() { return 'middleware2';}
        ];

        $order = new Routify\Order(
            '/route/for/something',
            Routify\Method::POST,
            function() { return 'ok'; },
            $middlewares
        );

        $this->assertTrue($order->hasAfter());
    }
} 