<?php
/**
 * @package KX
 * @subpackage Middleware\Auth
 */

declare(strict_types=1);

namespace KX\Middleware;

use KX\Core\Helper;
use KX\Model\Sessions as SessionsModel;
use KX\Model\Users as UsersModel;
use KX\Model\UserRoles as UserRolesModel;

class Auth extends \KX\Core\Middleware {

    public $auth = false;
    public $return = [];

    const LOGIN_ROUTE = '/hesap/giris';
    const ACCOUNT_ROUTE = '/hesap';

    public function __construct($container) {

        parent::__construct($container);

        $authCode = Helper::authCode();
        $recordedSession = (new SessionsModel())
            ->select('id, user_id, update_data')
            ->where('auth_code', $authCode)
            ->get();
        $session = Helper::getSession('user');

        if (! empty($recordedSession)) {

            /**
             * Sync updated data
             **/

            if (empty($session) OR ! is_null($recordedSession->update_data)) {

                if (empty($session)) {
                    $users = (new UsersModel());
                    $getUser = (new UsersModel())
                        ->select('id, user_name, first_name, last_name, email, password, token, role_id, status')
                        ->where('id', $recordedSession->user_id)
                        ->get();

                    $getUserRole = (new UserRolesModel())
                        ->select('routes, name')
                        ->where('id', $getUser->role_id)
                        ->get();

                    $getUser->role_name = $getUserRole->name;
                    $getUser->routes = (object) explode(',', $getUserRole->routes);
                    
                } else {
                    $getUser = $session;
                    if (is_string($recordedSession->update_data)) {
                        $recordedSession->update_data = json_decode($recordedSession->update_data);
                    }
                    
                    foreach ($recordedSession->update_data as $area => $val) {
                        $getUser->{$area} = $val;
                    }
                }
                $getUser = Helper::privateDataCleaner($getUser);

                Helper::setSession($getUser, 'user');
                $this->return['alerts'][] = [
                    'status' => 'success',
                    'message' => Helper::lang('base.login_information_updated'),
                ];
                $this->return['redirect'] = [$this->get('request')->uri, 0];
            }

            /**
             * Update check point 
             **/
            (new SessionsModel)->where('auth_code', $authCode)
                ->update([
                    'header' => Helper::getHeader(),
                    'ip' => Helper::getIp(),
                    'update_data' => null,
                    'last_action_date' => time(),
                    'last_action_point' => $this->get('request')->uri
                ]);

            $this->auth = true;

        } else {

            /**
             * Clear non-functional session data
             **/
            if (! empty($session)) {
                Helper::clearSession();
            }
        }
        
    }

    /**
     * Authority check for a endpoint
     * @param string $endpoint  
     * @return bool
     */
    public function authority ($endpoint) {

        global $endpoints;

        if ( is_null($endpoints)) 
            $endpoints = require Helper::path('app/External/endpoints.php');

        $endpoint = trim($endpoint, '/');
        $routes = Helper::userData('routes');


        if (! is_object($routes)) $routes = [];
        else $routes = (array)$routes;

        return $this->auth = (in_array($endpoint, $routes) !== false);

    }

    public function with() {

        $authenticated = false;
        if ($this->authority($this->get('endpoint'))) {
            $authenticated = true;
        }

        if ($this->auth AND $authenticated) {
            return array_merge($this->return, [
                'status' => true,
                'next'   => true
            ]);
        } else {
            return array_merge($this->return, [
                'status' => false,
                'statusCode' => 401,
                'next'   => false,
                'redirect' => self::LOGIN_ROUTE,
                'arguments' => [
                    'title' => Helper::lang('err'),
                    'error' => '401',
                    'output' => Helper::lang('error.unauthorized')
                ]
            ]);
        }

    }

    public function withOut() {

        if (! $this->auth) {
            return [
                'status' => true,
                'next'   => true
            ];
        } else {
            return [
                'status' => false,
                'next'   => false,
                'statusCode' => 302,
                'redirect' => self::ACCOUNT_ROUTE,
            ];
        }

    }

    public function verifyAccount() {

        if (isset($this->get('request')->params['verify-account']) !== false) {

            $token = $this->get('request')->params['verify-account'];

            $userModel = (new UsersModel());
            $getUser = $userModel
                ->where('status', 'passive')
                ->where('token', $token)
                ->get();

            if(! empty($getUser)) {

                $update = $userModel
                    ->where('id', $getUser->id)
                    ->update([
                        'token' => Helper::tokenGenerator(80),
                        'status' => 'active'
                    ]);

                if ($update) {

                    return [
                        'status' => false,
                        'next'   => false,
                        'statusCode' => 200,
                        'redirect' => '/',
                        'alerts' => [
                            [
                                'status' => 'success',
                                'message' => Helper::lang('base.verify_email_success')
                            ]
                        ]
                    ];

                } else {

                    return [
                        'status' => false,
                        'next'   => false,
                        'statusCode' => 200,
                        'redirect' => '/',
                        'alerts' => [
                            [
                                'status' => 'warning',
                                'message' => Helper::lang('base.verify_email_problem')
                            ]
                        ]
                    ];
                }

            } else {

                return [
                    'status' => false,
                    'next'   => false,
                    'statusCode' => 404,
                    'redirect' => '/',
                    'alerts' => [
                        [
                            'status' => 'error',
                            'message' => Helper::lang('base.verify_email_not_found')
                        ]
                    ]
                ];

            }

        } else {
            return [
                'status' => true,
                'next'   => true
            ];
        }

    }

}