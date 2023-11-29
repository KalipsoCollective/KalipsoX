<?php

/**
 * @package KX
 **/

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/app/bootstrap.php';

use KX\Core\Request;
use KX\Core\Response;

define('KX_VERSION', 'alpha');

try {

    $app = (new KX\Core\Factory)->setup();

    /**
     * Custom error handler
     **/
    /*
    $app->setCustomErrorHandler(function (Request $request, Response $response, $errNo, string $errMsg, string $file, int $line) {
        $response->setStatusCode(500);
        $response->setBody('<pre>Error: ' . $errMsg . ' in ' . $file . ' on line ' . $line . '</pre>');
        $response->send();
    }); */

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
