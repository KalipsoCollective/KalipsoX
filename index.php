<?php

/**
 * @package KX
 **/

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/bootstrap.php';

try {

    $app = (new KX\Core\Factory);

    // Single route
    $app->route('POST', '/', 'AppController@index', ['Auth@verifyAccount']);

    // Do not remove this route for the KN script library.
    $app->route('GET,POST', '/cron', 'AppController@cronJobs');

    // Multi route group
    $app->routes([
        ['GET,POST', '/sandbox', 'AppController@sandbox'],
        ['GET,POST', '/sandbox/:action', 'AppController@sandbox']
    ]);

    $app->excludeWhileInMaintenance([
        'auth/login'
    ]);
    $app->run();
} catch (Exception $e) {

    KX\Core\Exception::exceptionHandler($e);
}
