<?php

/**
 * @package KX
 * @subpackage Controller\Panel
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;
use KX\Core\Model;


use KX\Core\Request;
use KX\Core\Response;

use KX\Controller\Notification;

final class Panel
{

    public function dashboard(Request $request, Response $response)
    {
        return $response->render('panel/dashboard', [
            'title' => Helper::lang('base.dashboard'),
            'description' => Helper::lang('base.dashboard_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function settings(Request $request, Response $response)
    {
        return $response->render('panel/settings', [
            'title' => Helper::lang('base.settings'),
            'description' => Helper::lang('base.settings_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function users(Request $request, Response $response)
    {
        return $response->render('panel/users', [
            'title' => Helper::lang('base.users'),
            'description' => Helper::lang('base.users_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function userRoles(Request $request, Response $response)
    {
        return $response->render('panel/user_roles', [
            'title' => Helper::lang('base.user_roles'),
            'description' => Helper::lang('base.user_roles_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }
}
