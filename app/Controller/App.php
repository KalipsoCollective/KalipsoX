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

use KX\Controller\Notification;

use KX\Model\Sessions;

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
                Helper::base('/kalipso/clear-storage') => 'Clear the storage folder.',
            ]
        ];

        return $response->json($availableSubRoutes);
    }

    public function setupModels(Request $request, Response $response)
    {
        if (Helper::config('DEV_MODE', true) === false) {
            return $response->json([
                'status' => false,
                'message' => Helper::lang('base.dev_mode_only')
            ]);
        }
        $systemModel = new Model();
        $action = [
            'models' => $systemModel->setupModels()
        ];

        return $response->json($action);
    }

    public function setupModelsWithSeed(Request $request, Response $response)
    {
        if (Helper::config('DEV_MODE', true) === false) {
            return $response->json([
                'status' => false,
                'message' => Helper::lang('base.dev_mode_only')
            ]);
        }
        $systemModel = new Model();
        $action = [
            'models' => $systemModel->setupModels(true)
        ];

        return $response->json($action);
    }

    public function syncModels(Request $request, Response $response)
    {
        if (Helper::config('DEV_MODE', true) === false) {
            return $response->json([
                'status' => false,
                'message' => Helper::lang('base.dev_mode_only')
            ]);
        }
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

    public function cronJobs(Request $request, Response $response)
    {

        $return = [
            'status' => true,
            'jobs' => [],
        ];

        // emails
        $notification = new Notification();
        $return['jobs']['emails'] = $notification->sendEmails();

        // sessions
        $sessions = new Sessions();

        $getExpiredSessions = $sessions
            ->select('id, user_id, auth_token, ip, header')
            ->grouped(function ($q) {
                $q
                    ->orWhere('last_act_at', '<', strtotime('-1 month'))
                    ->whereNull('expire_at');
            })
            ->grouped(function ($q) {
                $q
                    ->orWhere('expire_at', '<', strtotime('-2 hours'))
                    ->whereNotNull('expire_at');
            })
            ->getAll();

        $return['jobs']['sessions'] = [
            'all' => [],
            'total_deleted' => 0,
            'total_expired' => 0,
        ];
        if (!empty($getExpiredSessions)) {
            foreach ($getExpiredSessions as $s) {

                $delete = $sessions->where('id', $s->id)->delete();
                $return['jobs']['sessions']['total_expired']++;
                if ($delete) {
                    $return['jobs']['sessions']['total_deleted']++;
                }
                $s->deleted = $delete;
                $return['jobs']['sessions']['all'][$s->id] = $s;
            }
        }

        return $response->json($return);
        // your cron jobs
    }

    public function clearStorage(Request $request, Response $response)
    {
        $dir = Helper::path('app/Storage');
        Helper::removeDir($dir);
        $action = [
            'status' => true,
            'message' => 'Storage cleared.'
        ];


        return $response->json($action);
    }
}
