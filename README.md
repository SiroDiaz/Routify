# SimpleRouter

A simple PHP router inspired in Express framework.


SimpleRouter is a fast and flexible router for PHP 5.4 and higher.

- Flexible regular expression routing (inspired by Express)
- Concrete use. Focused in do well one thing
- Easy to learn. Simple API that will remember you Express or Slim

### Getting started

1. PHP 5.4.x is required
2. Install SimpleRouter using Composer (recommended) or manually
3. Setup URL rewriting so that all requests are handled by index.php, for example, using an .htaccess file

### Example

```php
require 'vendor/autoload.php';

$router = new SimpleRouter/Router();

$router->get('/', function() {

});

$router->get('/post/:slug/:id', function($slug, $id) {
	echo "You are seeing the post nº $id, with title: $slug";
});

$router->post('/new', function() {
	// something for the POST /new
});

$router->put('/', function() {
	// something for the PUT /
});

$router->delete('/:id', function($id) {
	// something for the DELETE /:id
});

$router->run();
```