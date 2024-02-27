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
use KX\Model\UserRoles;
use KX\Model\Sessions;

use KX\Controller\Notification;

final class User
{

    public function account(Request $request, Response $response)
    {

        global $kxAuthToken, $kxSession;

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json' && !empty($kxAuthToken)) {

            $return = [
                'status' => true,
                'notify' => [],
            ];

            extract(Helper::input([
                'first_name' => 'nulled_text',
                'last_name' => 'nulled_text',
                'email' => 'email',
                'birthdate' => 'nulled_text',
                'password' => 'nulled_text',
                'password_again' => 'nulled_text',
            ], $request->getPostParams()));

            if (!empty($birthdate)) {
                $birthdate = (string)strtotime($birthdate);
            }

            $v = [
                'first_name' => ['value' => $first_name, 'pattern' => 'required|min:2|max:50'],
                'last_name' => ['value' => $last_name, 'pattern' => 'required|min:2|max:50'],
                'email' => ['value' => $email, 'pattern' => 'required|email'],
                'password' => ['value' => $password, 'pattern' => 'min:6|max:20'],
                'password_again' => ['value' => $password_again, 'pattern' => 'min:6|max:20|match:' . $password],
            ];

            if (empty($password)) {
                unset($v['password']);
                unset($v['password_again']);
            }

            $validation = Helper::validation($v);

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

                $userModel = new Users();
                $currentData = Helper::sessionData('user');

                if ($currentData) {
                    if ($currentData->status === 'deleted') {
                        $return['status'] = false;
                        $return['notify'][] = [
                            'type' => 'error',
                            'message' => Helper::lang('auth.your_account_deleted')
                        ];
                    } else {

                        $newData = [];
                        $emailUpdated = false;

                        if ($currentData->f_name !== $first_name) {
                            $newData['f_name'] = $first_name;
                        }

                        if ($currentData->l_name !== $last_name) {
                            $newData['l_name'] = $last_name;
                        }

                        if ($currentData->email !== $email) {
                            $newData['email'] = $email;
                            $emailUpdated = true;

                            // check other email
                            $checkEmail = $userModel->select('id')->where('email', $email)->get();
                            if ($checkEmail) {
                                $return['status'] = false;
                                $return['dom']['[name="email"]'] = [
                                    'addClass' => 'is-invalid',
                                ];
                                $return['dom']['[name="email"] ~ .invalid-feedback'] = [
                                    'text' => Helper::lang('auth.email_already_exists')
                                ];
                                return $response->json($return);
                            }
                        }

                        if (!empty($birthdate) && $birthdate !== $currentData->b_date) {
                            $newData['b_date'] = $birthdate;
                        }

                        if (!empty($password)) {
                            $newData['password'] = password_hash($password, PASSWORD_DEFAULT);
                        }

                        if (!empty($newData)) {

                            if ($emailUpdated) {
                                $newData['status'] = 'passive';
                                $newData['token'] = Helper::tokenGenerator(80);
                            }

                            $update = $userModel->where('id', $currentData->id)->update($newData);
                            if ($update) {

                                if ($emailUpdated) {
                                    $notificationController = new Notification();
                                    $notificationController->createNotification('email_change', [
                                        'id' => $currentData->id,
                                        'u_name' => $currentData->u_name,
                                        'email' => $email,
                                        'token' => $newData['token'],
                                    ]);
                                }
                                $return['notify'][] = [
                                    'type' => 'success',
                                    'message' => Helper::lang('auth.account_updated')
                                ];
                            } else {
                                $return['notify'][] = [
                                    'type' => 'error',
                                    'message' => Helper::lang('auth.a_problem_has_occurred')
                                ];
                            }
                        } else {
                            $return['notify'][] = [
                                'type' => 'warning',
                                'message' => Helper::lang('base.nothing_changed')
                            ];
                        }

                        return $response->json($return);
                    }
                } else {
                    $return['status'] = false;
                    $return['notify'][] = [
                        'type' => 'error',
                        'message' => Helper::lang('auth.account_not_found')
                    ];
                }
            }

            return $response->json($return);
        }


        return $response->render('auth/account', [
            'title' => Helper::lang('auth.account'),
            'description' => Helper::lang('auth.account_desc'),
            'headTitle' => Helper::lang('auth.account'),
            'headSubtitle' => Helper::lang('base.home'),
            'section' => 'account',
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function heartbeat(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'message' => 'OK',
            // 'heart_beat_stop' => true
        ];

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json') {
            if (empty(Helper::sessionData('user'))) {
                $return['status'] = false;
                $return['message'] = Helper::lang('auth.session_expired');
                $return['redirect'] = [
                    'url' => Helper::base('auth/login'),
                    'time' => 3000,
                    'direct' => true
                ];
            } else {
                // check notifications
                $notificationController = new Notification();
                $notificationCount = $notificationController->getUnreadNotificationCount(
                    Helper::sessionData('user')->id
                );

                $return['dom'] = [
                    '.notification-count' => [
                        'text' => $notificationCount > 99 ? '99+' : $notificationCount,
                        'removeClass' => $notificationCount > 0 ? 'd-none' : '',
                        'addClass' => $notificationCount > 0 ? '' : 'd-none',
                    ],
                    '.notification-dot' => [
                        'removeClass' => $notificationCount > 0 ? 'd-none' : '',
                        'addClass' => $notificationCount > 0 ? '' : 'd-none',
                    ],
                    '.notification-list' => [
                        'html' => $notificationController->getNotificationList(
                            Helper::sessionData('user')->id
                        )
                    ],
                ];
            }
        }



        return $response->json($return);
    }

    public function notificationAction(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'action' => 'nulled_text',
            'id' => 'nulled_text',
        ], $request->getRouteDetails()->attributes));

        if (!empty($action) && !empty($id)) {

            $notificationController = new Notification();
            $notification = $notificationController->getNotification($id);

            if ($notification && $notification->user_id == Helper::sessionData('user')->id) {
                $update = false;
                switch ($action) {
                    case 'view':
                        $update = $notificationController->updateNotification($id, [
                            'status' => 'viewed',
                            'viewed_at' => time(),
                        ]);
                        break;
                    case 'delete':
                        $update = $notificationController->updateNotification($id, [
                            'status' => 'deleted',
                            'deleted_at' => time(),
                        ]);
                        break;
                }

                if ($update) {
                    $return['dom'] = [
                        '.notification-' . $id => [
                            'remove' => true
                        ],
                        '.notification-list' => [
                            'html' => $notificationController->getNotificationList(
                                Helper::sessionData('user')->id
                            )
                        ],
                    ];
                } else {
                    $return['status'] = false;
                    $return['notify'][] = [
                        'type' => 'error',
                        'message' => Helper::lang('auth.a_problem_has_occurred')
                    ];
                }
            } else {
                $return['status'] = false;
                $return['notify'][] = [
                    'type' => 'error',
                    'message' => Helper::lang('auth.notification_not_found')
                ];
            }
        } else {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.notification_not_found')
            ];
        }

        if ($return['status']) {
            $return['heart_beat_direct'] = true;
        }

        return $response->json($return);
    }

    public function notifications(Request $request, Response $response)
    {
        $notificationController = new Notification();
        return $response->render('auth/account', [
            'title' => Helper::lang('base.notifications'),
            'description' => Helper::lang('base.notifications_desc'),
            'headTitle' => Helper::lang('auth.account'),
            'headSubtitle' => Helper::lang('base.notifications'),
            'section' => 'notifications',
            'notificationList' => $notificationController->getNotificationList(
                Helper::sessionData('user')->id
            ),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function sessions(Request $request, Response $response)
    {
        return $response->render('auth/account', [
            'title' => Helper::lang('auth.sessions'),
            'description' => Helper::lang('auth.sessions_desc'),
            'headTitle' => Helper::lang('auth.account'),
            'headSubtitle' => Helper::lang('auth.sessions'),
            'section' => 'sessions',
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function login(Request $request, Response $response)
    {

        global $kxAuthToken;

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json' && !empty($kxAuthToken)) {

            $return = [
                'status' => true,
                'notify' => [],
            ];

            extract(Helper::input([
                'username' => 'nulled_text',
                'password' => 'nulled_text',
                'remember_me' => 'check_as_boolean',
            ], $request->getPostParams()));

            $validation = Helper::validation(
                [
                    'username' => ['value' => $username, 'pattern' => 'required|min:3|max:20'],
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
                $checkAccount = $userModel
                    ->table('users as u')
                    ->select('
                        u.id, 
                        u.u_name, 
                        u.f_name, 
                        u.l_name,
                        u.email,
                        u.password,
                        u.role_id,
                        u.status,
                        r.name as role_name,
                        r.routes as role_routes
                    ')
                    ->leftJoin('user_roles as r', 'u.role_id', 'r.id')
                    ->where('u.u_name', $username)
                    ->orWhere('u.email', $username)
                    ->get();

                if ($checkAccount) {
                    if ($checkAccount->status === 'deleted') {
                        $return['status'] = false;
                        $return['dom']['[name="username"]'] = [
                            'addClass' => 'is-invalid',
                        ];
                        $return['dom']['[name="username"] ~ .invalid-feedback'] = [
                            'text' => Helper::lang('auth.your_account_deleted')
                        ];
                    } elseif (password_verify($password, $checkAccount->password)) {

                        // check session
                        $sessionModel = new Sessions();
                        $checkSession = $sessionModel
                            ->select('id')
                            ->where('user_id', $checkAccount->id)
                            ->where('auth_token', $kxAuthToken)
                            ->get();

                        if ($checkSession) {
                            $saveSession = $sessionModel
                                ->where('id', $checkSession->id)
                                ->update([
                                    'last_act_on' => $request->getUri(),
                                    'last_act_at' => time(),
                                    'expire_at' => $remember_me ? null : strtotime('+2 day'),
                                ]);
                        } else {
                            $saveSession = $sessionModel->insert([
                                'user_id' => $checkAccount->id,
                                'auth_token' => $kxAuthToken,
                                'ip' => Helper::getIp(),
                                'header' => Helper::getUserAgent(),
                                'last_act_on' => $request->getUri(),
                                'last_act_at' => time(),
                                'created_at' => time(),
                                'expire_at' => $remember_me ? null : strtotime('+2 day'),
                            ]);
                        }

                        if ($saveSession) {
                            $return['notify'][] = [
                                'type' => 'success',
                                'message' => Helper::lang('auth.login_success')
                            ];
                            $return['redirect'] = [
                                'url' => Helper::base('auth'),
                                'time' => 2000,
                                'direct' => true
                            ];
                        } else {
                            $return['notify'][] = [
                                'type' => 'error',
                                'message' => Helper::lang('auth.a_problem_has_occurred')
                            ];
                        }
                    } else {
                        $return['status'] = false;
                        $return['dom']['[name="password"]'] = [
                            'addClass' => 'is-invalid',
                        ];
                        $return['dom']['[name="password"] ~ .invalid-feedback'] = [
                            'text' => Helper::lang('auth.password_incorrect')
                        ];
                    }
                } else {
                    $return['status'] = false;
                    $return['notify'][] = [
                        'type' => 'error',
                        'message' => Helper::lang('auth.account_not_found')
                    ];
                }
            }

            return $response->json($return);
        }

        return $response->render('auth/login', [
            'title' => Helper::lang('auth.login'),
            'description' => Helper::lang('auth.login_desc'),
        ], 'auth');
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

                if (!$checkUsername && !empty(Helper::config('UNAVAILABLE_USERNAMES'))) {
                    $unavailableUsernames = explode(',', (string)Helper::config('UNAVAILABLE_USERNAMES'));
                    $checkUsername = in_array($username, $unavailableUsernames);
                }

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
                            'time' => 2000,
                            'direct' => false
                        ];
                    }
                }
            }

            return $response->json($return);
        }

        return $response->render('auth/register', [
            'title' => Helper::lang('auth.register'),
            'description' => Helper::lang('auth.register_desc'),
        ], 'auth');
    }

    public function verifyAccount(Request $request, Response $response)
    {

        extract(Helper::input([
            'token' => 'nulled_text',
        ], $request->getGetParams()));

        $alert = [
            'type' => 'danger',
            'message' => Helper::lang('auth.token_not_found')
        ];

        if (!empty($token)) {
            $userModel = new Users();
            $checkToken = $userModel
                ->select('id, status, u_name, email')
                ->where('token', $token)
                ->notWhere('status', 'deleted')
                ->get();
            if ($checkToken) {
                if ($checkToken->status === 'passive') {
                    $update = $userModel->where('id', $checkToken->id)->update([
                        'status' => 'active',
                        'token' => Helper::tokenGenerator(80),
                    ]);
                    if ($update) {
                        $alert = [
                            'type' => 'success',
                            'message' => Helper::lang('auth.account_verified')
                        ];

                        $notificationController = new Notification();
                        $notificationController->createNotification('account_verified', [
                            'id' => $checkToken->id,
                            'u_name' => $checkToken->u_name,
                        ]);
                    } else {
                        $alert = [
                            'type' => 'danger',
                            'message' => Helper::lang('auth.a_problem_has_occurred')
                        ];
                    }
                } else {
                    $alert = [
                        'type' => 'success',
                        'message' => Helper::lang('auth.account_already_verified')
                    ];
                }
            } else {
                $alert = [
                    'type' => 'danger',
                    'message' => Helper::lang('auth.account_not_found')
                ];
            }
        }
        $response->redirect(Helper::base('/'), 302, 3)->runRedirection();

        return $response->render('auth/verify', [
            'title' => Helper::lang('auth.verify_account'),
            'description' => Helper::lang('auth.verify_account_desc'),
            'alert' => $alert,
        ], 'auth');
    }

    public function recovery(Request $request, Response $response)
    {

        $step = 'request';

        if (!empty($request->getParam('token'))) {
            $step = 'reset';
        }

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json') {

            $return = [
                'status' => true,
                'notify' => [],
            ];

            extract(Helper::input([
                'email' => 'nulled_text',
                'password' => 'nulled_text',
                'password_again' => 'nulled_text',
            ], $request->getPostParams()));

            $validation = Helper::validation(
                $step === 'request' ?
                    [
                        'email' => ['value' => $email, 'pattern' => 'required|email'],
                    ] :
                    [
                        'password' => ['value' => $password, 'pattern' => 'required|min:6|max:20'],
                        'password_again' => ['value' => $password_again, 'pattern' => 'required|min:6|max:20|match:' . $password],
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

                if ($step === 'reset') { // password reset
                    $userModel = new Users();
                    $checkAccount = $userModel
                        ->select('id, status, u_name, email, token')
                        ->where('token', $request->getParam('token'))
                        ->get();

                    if ($checkAccount) {
                        if ($checkAccount->status === 'deleted') {
                            $return['status'] = false;
                            $return['dom']['[name="password"]'] = [
                                'addClass' => 'is-invalid',
                            ];
                            $return['dom']['[name="password"] ~ .invalid-feedback'] = [
                                'text' => Helper::lang('auth.your_account_deleted')
                            ];
                        } else {
                            $update = $userModel->where('id', $checkAccount->id)->update([
                                'password' => password_hash($password, PASSWORD_DEFAULT),
                                'token' => Helper::tokenGenerator(80),
                            ]);

                            if ($update) {
                                $return['status'] = true;
                                $return['notify'][] = [
                                    'type' => 'success',
                                    'message' => Helper::lang('auth.password_changed')
                                ];
                                $return['form_reset'] = true;
                                $return['redirect'] = [
                                    'url' => Helper::base('auth/login'),
                                    'time' => 3000,
                                    'direct' => false
                                ];

                                $notificationController = new Notification();
                                $notificationController->createNotification('recover_success', [
                                    'id' => $checkAccount->id,
                                    'u_name' => $checkAccount->u_name,
                                ]);
                            } else {
                                $return['status'] = false;
                                $return['notify'][] = [
                                    'type' => 'error',
                                    'message' => Helper::lang('auth.a_problem_has_occurred')
                                ];
                            }
                        }
                    } else {
                        $return['status'] = false;
                        $return['notify'][] = [
                            'type' => 'error',
                            'message' => Helper::lang('auth.token_not_found')
                        ];
                    }
                } else { // recovery request

                    // email control
                    $userModel = new Users();

                    $checkAccount = $userModel
                        ->select('
                        id, 
                        u_name, 
                        email,
                        token,
                        status
                    ')
                        ->where('email', $email)
                        ->get();


                    if ($checkAccount) {
                        if ($checkAccount->status === 'deleted') {
                            $return['status'] = false;
                            $return['dom']['[name="email"]'] = [
                                'addClass' => 'is-invalid',
                            ];
                            $return['dom']['[name="email"] ~ .invalid-feedback'] = [
                                'text' => Helper::lang('auth.your_account_deleted')
                            ];
                        } else {

                            $notificationController = new Notification();
                            $send = $notificationController->createNotification('recovery_request', [
                                'id' => $checkAccount->id,
                                'u_name' => $checkAccount->u_name,
                                'email' => $checkAccount->email,
                                'token' => $checkAccount->token,
                            ]);

                            if ($send) {
                                $return['status'] = true;
                                $return['notify'][] = [
                                    'type' => 'success',
                                    'message' => Helper::lang('auth.recovery_email_sent')
                                ];
                                $return['form_reset'] = true;
                            } else {
                                $return['status'] = false;
                                $return['notify'][] = [
                                    'type' => 'error',
                                    'message' => Helper::lang('auth.a_problem_has_occurred')
                                ];
                            }
                        }
                    } else {
                        $return['status'] = false;
                        $return['notify'][] = [
                            'type' => 'error',
                            'message' => Helper::lang('auth.account_not_found')
                        ];
                    }
                }
            }


            return $response->json($return);
        }

        return $response->render('auth/recovery', [
            'title' => Helper::lang('auth.recovery'),
            'description' => Helper::lang('auth.recovery_desc'),
            'step' => $step,
            'token' => $request->getParam('token'),
        ], 'auth');
    }

    public function logout(Request $request, Response $response)
    {
        global $kxAuthToken, $kxSession;

        $type = $request->getParam('type') ?? null;
        if ($type === 'all') {
            $sessionModel = new Sessions();
            $sessionModel
                ->where('user_id', $kxSession->user->id)
                ->delete();
        } elseif (is_numeric($type)) {
            $sessionModel = new Sessions();
            $sessionModel
                ->where('id', $type)
                ->where('user_id', $kxSession->user->id)
                ->delete();
        } else {
            $sessionModel = new Sessions();
            $sessionModel
                ->where('auth_token', $kxAuthToken)
                ->where('user_id', $kxSession->user->id)
                ->delete();
        }
        $currentUrl = $request->getUri();
        return $response->redirect($currentUrl, 302)->runRedirection();
    }
}
