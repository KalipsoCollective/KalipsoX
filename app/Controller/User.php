<?php

/**
 * @package KX
 * @subpackage Controller\User
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;
use KX\Core\Model;


use KX\Core\Request;
use KX\Core\Response;

use KX\Model\System;

final class User
{

    public function account(Request $request, Response $response)
    {
        return $response->json(['account' => 'account']);
    }

    public function login(Request $request, Response $response)
    {
        return $response->render('auth/login', [
            'title' => Helper::lang('auth.login'),
            'description' => Helper::lang('auth.login_desc'),
        ], 'layout');
    }

    public function register(Request $request, Response $response)
    {
        return $response->json(['register' => 'register']);
    }

    public function recovery(Request $request, Response $response)
    {
        return $response->json(['recovery' => 'recovery']);
    }

    public function logout(Request $request, Response $response)
    {
        return $response->json(['logout' => 'logout']);
    }
}
