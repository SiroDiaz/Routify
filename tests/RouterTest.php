<?php


class RouterTest extends PHPUnit_Framework_TestCase {

    private $router;

    public function __construct() {
        parent::__construct();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->router = new SimpleRouter\Router();
    }

    public function testGetPath() {
        $this->assertEquals($this->router->getPath(), '/');
    }

    public function testGetRequestMethod() {

        $this->assertEquals($this->router->getRequestMethod(), $_SERVER['REQUEST_METHOD']);
    }

    public function testSetPath() {
        $this->router->setPath('/api/v1');
        $this->assertEquals($this->router->getPath(), '/api/v1');
    }

    public function testFind() {
        $this->router->get('/', function() { return 'ok, this works'; });
        $this->assertEquals($this->router->find('/', SimpleRouter\Method::GET), true);

        // testing to retrieve the total of routes registered in the router
        $this->assertEquals(count($this->router->getRoutes()), 1);

        // testing to register unique routes
        $this->router->get('/', function() {});
        $this->router->get('/', function() {});
        $this->assertEquals(count($this->router->getRoutes()), 1);

        // testing that a new route is registered
        $this->router->get('/api/v1', function() {});
        $this->assertEquals(count($this->router->getRoutes()), 2);
    }

    public function testRun() {
        $this->router->setPath('/');
        $this->router->get('/', function() { return 'ok, this works'; });
        $this->assertSame($this->router->run(), 'ok, this works');

        $this->router->setRequestMethod("POST");
        $this->router->post('/', function() { return 'OK'; });
        $this->assertSame($this->router->run(), 'OK');

        // delete all routes registed
        $this->router->clear();
        $this->router->setPath('/any-unexisting-route/asd');
        $this->assertSame($this->router->run(), null);
    }

    public function testNotFound() {
        $this->router->setRequestMethod("GET");
        $this->router->setPath('/5');
        $this->router->notFound(function() {
            return "404";
        });

        $this->assertSame($this->router->run(), '404');
    }

}