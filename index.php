<?php

/**
 * @package KX
 **/

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/app/bootstrap.php';

define('KX_VERSION', 'alpha');

try {

    $app = (new KX\Core\Factory)->setup();

    /**
     * Single route
     **/
    $app->route(['GET', 'POST'], '/', 'AppController@index');

    /**
     * Multi route
     **/
    $app->routes([
        [['GET', 'POST'], '/', 'AppController@index'],
        [['GET', 'POST'], '/api', 'AppController@test'],
        [['GET', 'POST'], '/hi', function ($request, $response) {
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
