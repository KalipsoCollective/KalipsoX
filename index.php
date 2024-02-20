<?php

/**
 * @package KX
 **/

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/app/bootstrap.php';

use KX\Core\Request;
use KX\Core\Response;

define('KX_VERSION', 'alpha');

try {

    $app = (new KX\Core\Factory)->setup();

    // $app->response->setLayout('layout'); // set global layout
    // $app->setLayout('layout');

    /**
     * Custom error handler
     **/
    $app->setCustomErrorHandler(function (Request $request, Response $response, $errNo, string $errMsg, string $file, int $line) {
        $response->setStatus(500);
        $response->setBody('<pre>Error: ' . $errMsg . ' in ' . $file . ' on line ' . $line . '</pre>');
        $response->send();
    });

    /**
     * Set defaults
     **/
    $app->setDefaultViewFolder('basic');
    // $app->setDefaultViewLayout('layout');
    /*
    $app->setErrorPageContents([
        '404' => ['']
    ]);*/

    /**
     * Single route
     **/
    $app->route(['GET'], '/', 'App@index', ['Test@run']);

    /**
     * Route group
     **/
    $app->routeGroup(['GET', '/auth', 'User@account', 'Auth@isLogged'], [
        [['POST', 'GET'], '/register', 'User@register', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/verify-account', 'User@verifyAccount'],
        [['POST', 'GET'], '/login', 'User@login', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/recovery', 'User@recovery', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/logout', 'User@logout', 'Auth@isLogged'],
        [['POST', 'GET'], '/logout/:type', 'User@logout', 'Auth@isLogged'],
    ]);

    // inital setup and app routes
    $app->routeGroup(['GET', '/kalipso', 'App@setup'], [
        ['GET', '/setup-models', 'App@setupModels'],
        ['GET', '/setup-models-with-seed', 'App@setupModelsWithSeed'],
        ['GET', '/sync-models', 'App@syncModels'],
        ['GET', '/clear-storage', 'App@clearStorage'],
        ['GET', '/cron', 'App@cronJobs'],
    ]);

    $app->run();
} catch (Exception $e) {

    KX\Core\Exception::exceptionHandler($e);
}
