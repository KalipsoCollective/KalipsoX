# KalipsoX

## Introduction

KalipsoX is a simple and powerful PHP framework. You can quickly build your applications on it.

## Requirements

- Apache >= 2.4.5 or Nginx >= 1.8
- PHP >= 7.1
- MySQL >= 5.6

## Documentation

This documentation has been created to give you a quick start.

### Installation

- `composer install`
- Visit http://localhost/kalipso
- Prepare DB
- Seed DB
- Start Development

### Tricks

#### Server Configurations (Apache .htaccess)

```htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php [L]
</IfModule>
```

#### Server Configurations (Nginx nginx.conf)

```nginx_conf
location / {
	if (!-e $request_filename){
		rewrite ^(.+)$ /index.php/$1 break;
	}
}
```

### Routing

You can refer to the examples in index.php for adding new routes and handlers.

For example;

```php

    try {
        $app = (new KX\Core\Factory)->setup();

        /**
         * Custom error handler
         **/
        $app->setCustomErrorHandler(function (Request $request, Response $response, $errNo, string $errMsg, string $file, int $line) {
            $response->setStatusCode(500);
            $response->setBody('<pre>Error: ' . $errMsg . ' in ' . $file . ' on line ' . $line . '</pre>');
            $response->send();
        });

        /**
         * Single route
         **/
        $app->route(['POST'], '/', 'AppController@index');

        /**
         * Multi route
         **/
        $app->routes([
            [['GET', 'POST'], '/test', 'AppController@test'],
            [['GET', 'POST'], '/hi', function (Request $request, Response $response) {
              // direct string response
              // return $response->setBody('Hello');

              // render view
              return $response->render('basic/hi', [
                  'title' => 'Hello World!',
                  'description' => 'This is a sample index page.',
              ]);

              // render view with layout
              // return $response->render('basic/hi', [
              //     'title' => 'Hello World!',
              //     'description' => 'This is a sample index page.',
              // ], 'layout');

              // json response
              // return $response->json([
              //     'title' => 'Hello World!',
              //     'description' => 'This is a sample index page.',
              // ]);
            }],
        ]);

        /**
         * Route group
         **/
        $app->routeGroup(['GET', '/auth', 'UserController@account', 'UserMiddleware@root'], [
            ['GET', '/login', 'UserController@login'],
            ['GET', '/register', 'UserController@register'],
            ['GET', '/recovery', 'UserController@recovery'],
            [['POST', 'GET'], '/logout', 'UserController@logout', ['UserMiddleware@isLogged', 'UserMiddleware@isLoggedAsAdmin']],
            ['GET', '/:action', 'UserController@account'],
            ['GET', '/:action', 'UserController@account'],
        ]);

        $app->run();

    } catch (Exception $e) {

        KX\Core\Exception::exceptionHandler($e);
    }
```
