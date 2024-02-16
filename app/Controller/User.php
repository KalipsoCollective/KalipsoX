<?php

/**
 * @package KX
 * @subpackage Controller\User
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;


use KX\Core\Request;
use KX\Core\Response;

use KX\Model\Users;

use KX\Controller\Notification;

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

            $return = [
                'status' => true,
                'notify' => [],
            ];

            if (Helper::config('settings.registration_system', true) !== true) {
                $return['status'] = false;
                $return['notify'][] = [
                    'type' => 'error',
                    'message' => Helper::lang('auth.registration_system_disabled')
                ];
                return $response->json($return);
            }

            extract(Helper::input([
                'username' => 'nulled_text',
                'email' => 'nulled_email',
                'password' => 'nulled_text',
            ], $request->getPostParams()));

            $validation = Helper::validation(
                [
                    'username' => ['value' => $username, 'pattern' => 'required|min:3|max:20|alphanumeric'],
                    'email' => ['value' => $email, 'pattern' => 'required|email'],
                    'password' => ['value' => $password, 'pattern' => 'required|min:6|max:20'],
                ]
            );

            if (!empty($validation)) {
                $return['dom'] = [];
                foreach ($validation as $field => $messages) {
                    $return['dom']['[name="' . $field . '"]'] = [
                        'addClass' => 'is-invalid',
                    ];

                    $return['dom']['[name="' . $field . '"] ~ .invalid-feedback'] = [
                        'text' => implode(' ', $messages)
                    ];
                }
                $return['status'] = false;
                $return['notify'][] = [
                    'type' => 'error',
                    'message' => Helper::lang('form.fill_all_fields')
                ];
            } else {
                // username and email control
                $userModel = new Users();

                $checkUsername = $userModel->select('id')->where('u_name', $username)->get();
                $checkEmail = $userModel->select('id')->where('email', $email)->get();
                if ($checkUsername) {
                    $return['status'] = false;
                    $return['dom']['[name="username"]'] = [
                        'addClass' => 'is-invalid',
                    ];
                    $return['dom']['[name="username"] ~ .invalid-feedback'] = [
                        'text' => Helper::lang('auth.username_already_exists')
                    ];
                }

                if ($checkEmail) {
                    $return['status'] = false;
                    $return['dom']['[name="email"]'] = [
                        'addClass' => 'is-invalid',
                    ];
                    $return['dom']['[name="email"] ~ .invalid-feedback'] = [
                        'text' => Helper::lang('auth.email_already_exists')
                    ];
                }

                if (!$checkUsername && !$checkEmail) {

                    $row = [
                        'u_name' => $username,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'token' => Helper::tokenGenerator(80),
                        'role_id' => Helper::config('settings.default_user_role') ?? 0,
                        'created_at' => time(),
                    ];

                    $insert = $userModel->insert($row);
                    if (!$insert) {
                        $return['status'] = false;
                        $return['notify'][] = [
                            'type' => 'error',
                            'message' => Helper::lang('auth.a_problem_has_occurred')
                        ];
                    } else {

                        $row['id'] = $insert;
                        $notificationController = new Notification();
                        $notificationController->createNotification('welcome', $row);

                        $return['notify'][] = [
                            'type' => 'success',
                            'message' => Helper::lang('auth.register_success')
                        ];
                        $return['redirect'] = [
                            'url' => Helper::base('auth/login'),
                            'time' => 3000,
                            'direct' => false
                        ];
                    }
                }
            }

            return $response->json($return);

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
