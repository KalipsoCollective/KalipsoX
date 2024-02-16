<?php

/**
 * @package KX
 * @subpackage Controller\App
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;
use KX\Core\Model;


use KX\Core\Request;
use KX\Core\Response;

final class App
{

    public function setup(Request $request, Response $response)
    {

        $availableSubRoutes = [
            'setup_routes' => [
                Helper::base('/kalipso/setup-models') => 'Set up the entire database with model classes.',
                Helper::base(
                    '/kalipso/setup-models-with-seed'
                ) => 'Set up the entire database with model classes and import sample data as well.',
                Helper::base(
                    '/kalipso/sync-models'
                ) => 'Synchronize the database for new column definitions.',
            ]
        ];

        return $response->json($availableSubRoutes);
    }

    public function setupModels(Request $request, Response $response)
    {
        $systemModel = new Model();
        $action = [
            'models' => $systemModel->setupModels()
        ];

        return $response->json($action);
    }

    public function setupModelsWithSeed(Request $request, Response $response)
    {
        $systemModel = new Model();
        $action = [
            'models' => $systemModel->setupModels(true)
        ];

        return $response->json($action);
    }

    public function syncModels(Request $request, Response $response)
    {
        $systemModel = new Model();
        $action = [
            'models' => $systemModel->syncModels()
        ];

        return $response->json($action);
    }

    public function index(Request $request, Response $response)
    {
        return $response->send('<pre>Start your awesome project.</pre>');
    }

    public function cronJobs()
    {

        // your cron jobs
    }
}
