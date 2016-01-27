<?php

class RouterParserTest extends PHPUnit_Framework_TestCase {
    private $routerParser;

    public function __construct() {
        parent::__construct();
        $this->routerParser = new Routify\RouterParser('/api/v1/user/2322132');
    }

    public function testGetPath() {
        $this->assertEquals($this->routerParser->getPath(), '/api/v1/user/2322132');
    }

    public function testHasParams() {
        $this->assertTrue($this->routerParser->hasParams('/api/v1/user/:id'));
        $this->assertFalse($this->routerParser->hasParams('/api/v1/user/id'));
    }

    public function testGetParams() {
        // must return an empty array because there is no any parameter in the pattern uri.
        $this->assertSame($this->routerParser->getParams('/api/v1/user/id'), []);
        // must return an array with an id equal to 2322132
        $this->assertSame($this->routerParser->getParams('/api/v1/user/:id'), ['id' => '2322132']);
    }

    public function testCountParams() {
        $this->assertEquals($this->routerParser->countParams('/api/v1/status/:user/:status'), 2);
    }

    public function testMatchParameters() {
        $this->assertFalse($this->routerParser->match('/api/v1/user'));
        //
        $this->assertTrue($this->routerParser->match('/api/v1/user/:id'));
        // test alterned parameters
        $this->routerParser->setPath('/api/v1/123/p/hello-world');
        $this->assertTrue($this->routerParser->match('/api/v1/:id/p/:slug'));
        // test root path
        $this->routerParser->setPath('/');
        $this->assertTrue($this->routerParser->match('/'));
        // test regular expressions
        $this->routerParser->setPath('/api/vi/posts/hello-world/1234');
        $this->assertTrue($this->routerParser->match('/api/vi/posts/[a-zA-Z0-9-]+/[0-9]+'));
    }
} 