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
        if ($request->getRequestMethod() === 'POST') {

            exit;
        }

        return $response->render('auth/login', [
            'title' => Helper::lang('auth.login'),
            'description' => Helper::lang('auth.login_desc'),
        ], 'layout');
    }

    public function register(Request $request, Response $response)
    {

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json') {

            extract(Helper::input([
                'username' => 'nulled_text',
                'email' => 'nulled_email',
                'password' => 'nulled_text',
            ], $request->getPostParams()));

            if (empty($username) || empty($email) || empty($password)) {
                return $response->json(
                    [
                        'alerts' => [
                            [
                                'type' => 'error',
                                'message' => Helper::lang('form.fill_all_fields')
                            ],

                        ]
                    ]
                );
            }



            return $response->json(['register' => 'register']);
        }

        return $response->render('auth/register', [
            'title' => Helper::lang('auth.register'),
            'description' => Helper::lang('auth.register_desc'),
        ], 'layout');
    }

    public function recovery(Request $request, Response $response)
    {
        return $response->render('auth/recovery', [
            'title' => Helper::lang('auth.recovery'),
            'description' => Helper::lang('auth.recovery_desc'),
        ], 'layout');
    }

    public function logout(Request $request, Response $response)
    {
        return $response->json(['logout' => 'logout']);
    }
}
