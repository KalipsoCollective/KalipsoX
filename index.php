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
exit;

try {

    $app = (new KX\Core\Factory)->setup();

    // $app->response->setLayout('layout'); // set global layout
    $app->setLayout('layout');

    /**
     * Custom error handler
     **/
    /*
    $app->setCustomErrorHandler(function (Request $request, Response $response, $errNo, string $errMsg, string $file, int $line) {
        $response->setStatusCode(500);
        $response->setBody('<pre>Error: ' . $errMsg . ' in ' . $file . ' on line ' . $line . '</pre>');
        $response->send();
    });

    /**
     * Single route
     **/
    $app->route(['GET'], '/', 'App@index', ['Test@run']);

    /**
     * Multi route
     **/
    $app->routes([
        [['GET', 'POST'], '/test', 'AppController@test'],
        [['GET', 'POST'], '/hi', function (Request $request, Response $response, $factory) {
            $response->setBody('Hi from test!');

            return $response->render('basic/hi', [
                'title' => 'Hi from test!',
                'description' => 'This is a description.',
            ]);
        }],
        [['GET', 'POST'], '/hi/:val', function (Request $request, Response $response, $factory) {
            $response->setBody('Hi!');

            return $response->render('basic/hi', [
                'title' => 'Hi ' . $request->getParam('val') . '!',
                'description' => 'This is a description.',
            ]);
        }],
        [['GET', 'POST'], '/hi/werwer', function (Request $request, Response $response, $factory) {
            $response->setBody('Hi!');

            return $response->render('basic/hi', [
                'title' => 'Hi ' . $request->getParam('val') . '!',
                'description' => 'This is a description.',
            ]);
        }],
        [['GET', 'POST'], '/hi/:test', function (Request $request, Response $response, $factory) {
            $response->setBody('Hi!');

            return $response->render('basic/hi', [
                'title' => 'Hi ' . $request->getParam('val') . '!',
                'description' => 'This is a description.',
            ]);
        }],
        [['GET', 'POST'], '/ho', function (Request $request, Response $response, $factory) {

            return $response->json([
                'title' => 'Hi!',
                'description' => 'This is a description.',
            ]);
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

    // inital setup routes
    $app->routeGroup(['GET', '/app', 'App@setup'], [
        ['GET', '/models', 'App@setupModels'],
        ['GET', '/models-with-seed', 'App@setupModelsWithSeed'],
        ['GET', '/sync-models', 'App@syncDatabase'],
    ]);

    $app->run();
} catch (Exception $e) {

    KX\Core\Exception::exceptionHandler($e);
}
