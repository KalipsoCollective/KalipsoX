<?php

/**
 * @package KX
 **/

declare(strict_types=1);



require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 'On');
ini_set('error_log', KX\Core\Helper::path('app/Storage'));
error_reporting(E_ALL);

use KX\Core\Request;
use KX\Core\Response;

define('KX_VERSION', 'v1.0.0');

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

    $app->setMaintenanceMode([
        '/auth/login',
    ], 'dashboard.settings');

    /**
     * Single route
     **/
    $app->route(['GET'], '/', 'App@index', ['Test@run']);

    /**
     * Route group
     **/
    $app->routeGroup([['POST', 'GET'], '/auth', 'User@account', 'Auth@isLogged'], [
        [['POST', 'GET'], '/register', 'User@register', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/verify-account', 'User@verifyAccount'],
        [['POST', 'GET'], '/login', 'User@login', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/recovery', 'User@recovery', 'Auth@isNotLogged'],
        [['POST', 'GET'], '/notifications', 'User@notifications', 'Auth@isLogged'],
        [['POST', 'GET'], '/sessions', 'User@sessions', 'Auth@isLogged'],
        [['POST', 'GET'], '/logout', 'User@logout', 'Auth@isLogged'],
        [['POST', 'GET'], '/logout/:type', 'User@logout', 'Auth@isLogged'],
        [['POST'], '/heartbeat', 'User@heartbeat', 'Auth@isLogged'],
        [['POST'], '/notifications/:action/:id', 'User@notificationAction', 'Auth@isLogged'],
    ]);

    $app->routeGroup(['GET', '/dashboard', 'Panel@dashboard', 'Auth@isAuthorized'], [
        [['POST'], '/data/:table', 'Panel@tableData', 'Auth@isAuthorized'],
        [['POST', 'GET'], '/settings', 'Panel@settings', 'Auth@isAuthorized'],
        // Users
        [['POST', 'GET'], '/users', 'Panel@users', 'Auth@isAuthorized'],
        [['POST'], '/users/add', 'Panel@userAdd', 'Auth@isAuthorized'],
        [['POST', 'GET'], '/users/edit/:id', 'Panel@userEdit', 'Auth@isAuthorized'],
        [['POST'], '/users/delete/:id', 'Panel@userDelete', 'Auth@isAuthorized'],
        // User Roles
        [['POST', 'GET'], '/user-roles', 'Panel@userRoles', 'Auth@isAuthorized'],
        [['POST'], '/user-roles/add', 'Panel@userRoleAdd', 'Auth@isAuthorized'],
        [['POST', 'GET'], '/user-roles/edit/:id', 'Panel@userRoleEdit', 'Auth@isAuthorized'],
        [['POST'], '/user-roles/delete/:id', 'Panel@userRoleDelete', 'Auth@isAuthorized'],
        // Sessions
        [['POST', 'GET'], '/sessions', 'Panel@sessions', 'Auth@isAuthorized'],
        [['POST'], '/sessions/delete/:id', 'Panel@sessionDelete', 'Auth@isAuthorized'],
        // Logs
        [['POST', 'GET'], '/logs', 'Panel@logs', 'Auth@isAuthorized'],
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
