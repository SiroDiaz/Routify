<?php

require 'vendor/autoload.php';

$router = new SimpleRouter\Router();
// echo rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$router->get('/', function() {
   echo "Ok, this works";
});

$router->run();