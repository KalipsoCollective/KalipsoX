<?php
/**
 * @package KX
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Controller;
use KX\Core\Helper;
use KX\Model\Users as UsersModel;
use KX\Model\UserRoles as UserRolesModel;
use KX\Model\Sessions as SessionsModel;
use KX\Controller\NotificationController as Notification;

final class UserController extends Controller {

    const LOGIN_ROUTE = '/hesap/giris';
    const ACCOUNT_ROUTE = '/hesap';
    const RECOVERY_ROUTE = '/hesap/kurtar';

    public function login() {

        $alerts = [];

        if ($this->get('request')->method === 'POST') {

            extract(Helper::input([
                'username' => 'nulled_text',
                'password' => 'nulled_text'
            ], $this->get('request')->params));

            if (! is_null($username) AND ! is_null($password)) {

                $users = (new UsersModel());

                $getUser = $users->select('id, user_name, first_name, last_name, email, password, token, role_id, details, status')
                    ->where('user_name', $username)->orWhere('email', $username)
                    ->get();

                if ( ! empty($getUser)) {

                    if ($getUser->status == 'deleted') {

                        $alerts[] = [
                            'status' => 'error',
                            'message' => Helper::lang('base.your_account_has_been_blocked')
                        ];

                    } else {

                        if (password_verify($password, $getUser->password)) {

                            $userRoles = (new UserRolesModel());
                            $getUserRole = $userRoles
                                ->select('routes, name')
                                ->where('id', $getUser->role_id)
                                ->get();

                            if (! empty($getUserRole)) {

                                $getUser->role_name = $getUserRole->name;
                                $getUser->routes = (object) explode(',', $getUserRole->routes);

                            }
                            $getUser = Helper::privateDataCleaner($getUser);

                            $sessions = (new SessionsModel());
                            $logged = $sessions->insert([
                                'auth_code' => Helper::authCode(),
                                'user_id' => $getUser->id,
                                'header' => Helper::getHeader(),
                                'ip' => Helper::getIp(),
                                'role_id' => $getUser->role_id,
                                'last_action_date' => time(),
                                'last_action_point' => $this->get('request')->uri
                            ]);

                            if ($logged) {

                                Helper::setSession($getUser, 'user');
                                $alerts[] = [
                                    'status' => 'success',
                                    'message' => Helper::lang('base.welcome_back'),
                                ];

                                $redirect = self::ACCOUNT_ROUTE;

                            } else {

                                $alerts[] = [
                                    'status' => 'warning',
                                    'message' => Helper::lang('base.login_problem')
                                ];

                            }

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Helper::lang('base.your_login_info_incorrect')
                            ];

                        }

                    }


                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Helper::lang('base.account_not_found')
                    ];

                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Helper::lang('base.form_cannot_empty')
                ];

            }
            
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Helper::lang('base.login'),
                'description' => Helper::lang('base.login_message')
            ],
            'alerts' => $alerts,
            'view' => 'auth.login',
        ];

        if (isset($redirect)) {
            $return['redirect'] = $redirect;
        }

        return $return;

    }

    public function account() {

        $steps = [
            'profil' => [
                'icon' => 'ti ti-tool', 'lang' => 'base.profile'
            ], 
            'oturumlar' => [
                'icon' => 'ti ti ti-devices', 'lang' => 'base.sessions'
            ],
        ];

        $action = '';

        if (isset($this->get('request')->attributes['action']) !== false)
            $action = $this->get('request')->attributes['action'];

        $title = Helper::lang('base.account');
        $output = '';
        $alerts = [];
        $statusCode = 200;

        switch ($action) {
            case 'profil':
                $head = Helper::lang('base.profile');
                $title = $head . ' | ' . $title;
                $description = Helper::lang('base.profile_message');
                $output = Helper::getSession('user');

                if ($this->get('request')->method === 'POST' && is_object($output)) {

                    extract(Helper::input([
                        'email' => 'nulled_email', 
                        'first_name' => 'nulled_text', 
                        'last_name' => 'nulled_text',
                        'details' => 'nulled_text',
                        'password' => 'nulled_password'
                    ], $this->get('request')->params));

                    if (! is_null($email) AND ! is_null($first_name) AND ! is_null($last_name) AND ! is_null($details)) {

                        if (is_array($details)) {
                            if (isset($details['birth_date']) !== false) {
                                $details['birth_date'] = strtotime($details['birth_date']);
                            }
                        } else {
                            $details = [];
                        }

                        $check = (new UsersModel)->select('id')
                            ->where('email', $email)
                            ->notWhere('id', Helper::userData('id'))
                            ->get();

                        if (empty($check)) {

                            $newData = [
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'details' => json_encode($details)
                            ];

                            if ($password)
                                $newData['password'] = $password;

                            if (Helper::userData('email') !== $email) {
                                $newData['email'] = $email;
                                $newData['status'] = 'passive';
                                $sendLink = true;
                            }

                            $update = (new UsersModel)
                                ->where('id', $output->id)
                                ->update($newData);

                            $newData['details'] = json_decode($newData['details']);

                        } else 
                            $update = false;

                        if ($update) {

                            (new SessionsModel)
                                ->where('user_id', $output->id)
                                ->update(
                                    ['update_data' => json_encode($newData)]
                                );

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Helper::lang('base.save_success')
                            ];
                            $redirect = '/hesap/profil';

                            if (isset($sendLink)) {

                                $args = (array) Helper::getSession('user');
                                $args['changes'] = '
                                <span style="color: red;">' . Helper::userData('email') . '</span> → 
                                <span style="color: green;">' . $email . '</span>';
                                $args = array_merge($args, $newData);
                                (new NotificationController($this->get()))->add('email_change', $args);

                            }

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Helper::lang('base.save_problem')
                            ];
                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Helper::lang('base.form_cannot_empty')
                        ];

                    }

                }

                break;

            case 'oturumlar':
                $head = Helper::lang('base.sessions');
                $title = $head . ' | ' . $title;
                $description = Helper::lang('base.sessions_message');
                $output = [];
                $sessions = (new SessionsModel());

                $session = Helper::getSession('user');
                $authCode = Helper::authCode();
                if (is_object($session) AND isset($session->id) !== false) {

                    $records = $sessions->select('id, header, auth_code, ip, last_action_date, last_action_point')
                        ->where('user_id', $session->id)
                        ->getAll();

                    if ($records) {
                        $output = [];
                        foreach ($records as $record) {

                            if (isset($this->get('request')->params['terminate']) !== false AND $record->id == $this->get('request')->params['terminate']) {

                                $delete = $sessions->where('id', $record->id)->delete();
                                if ($authCode != $record->auth_code AND $delete) {
                                    $alerts[] = [
                                        'status' => 'success',
                                        'message' => Helper::lang('base.session_terminated')
                                    ];
                                    continue;
                                } else {

                                    $alerts[] = [
                                        'status' => 'warning',
                                        'message' => Helper::lang('base.session_not_terminated')
                                    ];
                                }
                            }

                            $record->device = Helper::userAgentDetails($record->header);
                            unset($record->header);
                            $output[] = $record;
                        }
                    }
                }

                break;

            case '':
                $head = Helper::lang('base.account');
                $description = Helper::lang('base.account_message');
                break;
            
            default:
                $head = Helper::lang('base.account');
                $description = Helper::lang('base.account_message');
                $alerts[] = [
                    'status' => 'warning',
                    'message' => Helper::lang('error.page_not_found')
                ];
                $redirect = self::ACCOUNT_ROUTE;
                $statusCode = 404;
                break;
        }

        $return = [
            'status' => true,
            'statusCode' => $statusCode,
            'arguments' => [
                'title' => $title,
                'head'  => $head,
                'description' => $description,
                'output' => $output,
                'steps' => $steps,
                'action' => $action
            ],
            'alerts' => $alerts,
            'view' => 'auth.account'
        ];

        if (isset($session) !== false)
            $return['arguments']['session'] = $session;

        if (isset($authCode) !== false)
            $return['arguments']['auth_code'] = $authCode;

        if (isset($redirect) !== false)
            $return['redirect'] = $redirect;

        return $return;

    }

    public function register() {

        $alerts = [];

        if ($this->get('request')->method === 'POST') {

            extract(Helper::input([
                'email' => 'nulled_email', 
                'username' => 'nulled_text',
                'name' => 'nulled_text',
                'surname' => 'nulled_text',
                'password' => 'nulled_text'
            ], $this->get('request')->params));

            if (! is_null($username) AND ! is_null($email) AND ! is_null($password)) {

                $users = (new UsersModel());

                $getWithEmail = $users->select('email')->where('email', $email)->get();
                if ( !$getWithEmail) {

                    $getWithUsername = $users->select('user_name')->where('user_name', $username)->get();
                    if ( !$getWithUsername) {

                        $row = [
                            'user_name' => $username,
                            'first_name' => $name,
                            'last_name' => $surname,
                            'email' => $email,
                            'password' => password_hash($password, PASSWORD_DEFAULT),
                            'token'  => Helper::tokenGenerator(80),
                            'role_id'   => Helper::config('settings.default_user_role'),
                            'created_at'=> time(),
                            'status' => 'passive'
                        ];

                        $insert = $users->insert($row);

                        if ($insert) {

                            $row['id'] = $insert;
                            (new Notification($this->get()))->add('registration', $row);

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Helper::lang('base.registration_successful')
                            ];
                            $redirect = [$this->get()->url(self::LOGIN_ROUTE), 4];

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Helper::lang('base.registration_problem')
                            ];

                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Helper::lang('base.username_is_already_used')
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Helper::lang('base.email_is_already_used')
                    ];

                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Helper::lang('base.form_cannot_empty')
                ];

            }
            
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Helper::lang('base.register'),
                'description' => Helper::lang('base.register_message')
            ],
            'alerts' => $alerts,
            'view' => 'auth.register',
        ];

        if (isset($redirect))
            $return['redirect'] = $redirect;

        return $return;

    }

    public function logout() {

        
        $deleteSession = (new SessionsModel)
            ->where('auth_code', Helper::authCode())
            ->delete();

        if ($deleteSession !== false AND $deleteSession !== null) {

            Helper::clearSession();
            return [
                'status' => true,
                'alerts' => [[
                    'status' => 'success',
                    'message' => Helper::lang('base.signed_out'),
                ]],
                'redirect' => self::LOGIN_ROUTE,
                'view' => null
            ];

        } else {

            return [
                'status' => false,
                'statusCode' => 401,
                'arguments' => [
                    'title' => Helper::lang('err'),
                    'error' => '401',
                    'output' => Helper::lang('error.a_problem_occurred') . ' -> (logout)'
                ],
                'view' => ['error', 'error']
            ];
        }

    }

    public function recovery() {

        $alerts = [];
        $step = 1;

        if ($this->get('request')->method === 'POST') {

            extract(Helper::input([
                'email' => 'nulled_email',
                'password' => 'nulled_text',
                'token' => 'nulled_text',
            ], $this->get('request')->params));

            if (! is_null($email) AND (is_null($password) AND is_null($token))) { // Step 1: Request 

                $users = (new UsersModel());
                $getUser = $users->select('id, token, status, first_name, user_name, email')
                    ->where('email', $email)
                    ->notWhere('status', 'deleted')
                    ->get();

                if ( ! empty($getUser) ) {

                    if ($getUser->status === 'active') {

                        $sendLink = (new Notification($this->get()))->add('recovery_request', $getUser);
                        if ($sendLink) {

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Helper::lang('base.recovery_request_successful')
                            ];
                            $redirect = $this->get()->url(self::LOGIN_ROUTE);

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Helper::lang('base.recovery_request_problem')
                            ];

                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Helper::lang('base.account_not_verified')
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Helper::lang('base.account_not_found')
                    ];

                }

            } elseif (is_null($email) AND (! is_null($password) AND ! is_null($token))) { // Step 3: Reset

                $users = (new UsersModel());
                $getUser = $users->select('id, token, status, first_name, user_name, email')->where('token', $token)->where('status', 'active')->get();
                if (! empty($getUser)) {

                    $update = $users->where('id', $getUser->id)
                        ->update([
                            'password' => Helper::filter($password, 'password'),
                            'token' => Helper::tokenGenerator(80)
                        ]);

                    if ($update) {

                        (new Notification($this->get()))->add('account_recovered', $getUser);
                        $alerts[] = [
                            'status' => 'success',
                            'message' => Helper::lang('base.account_recovered')
                        ];
                        $redirect = $this->get()->url(self::LOGIN_ROUTE);

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Helper::lang('base.account_not_recovered')
                        ];
                    }

                } else {

                    $alerts[] = [
                        'status' => 'error',
                        'message' => Helper::lang('base.account_not_found')
                    ];
                    $redirect = $this->get()->url(self::RECOVERY_ROUTE);
                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Helper::lang('base.form_cannot_empty')
                ];

            }
            
        } elseif (isset($this->get('request')->params['token']) !== false) { // Step 2: Verify

            extract(Helper::input([
                'token' => 'nulled_text',
            ], $this->get('request')->params));

            $users = (new UsersModel());
            $getUser = $users->select('id')->where('token', $token)->where('status', 'active')->get();
            if (! empty($getUser)) {

                $step = 2;

            } else {

                $alerts[] = [
                    'status' => 'error',
                    'message' => Helper::lang('base.account_not_found')
                ];
                $redirect = $this->get()->url(self::RECOVERY_ROUTE);
            }
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Helper::lang('base.recovery_account'),
                'description' => Helper::lang('base.recovery_account_message'),
                'step' => $step,
            ],
            'alerts' => $alerts,
            'view' => 'auth.recovery',
        ];

        if (isset($redirect))
            $return['redirect'] = $redirect;

        if (isset($token))
            $return['arguments']['token'] = $token;

        return $return;

    }

}