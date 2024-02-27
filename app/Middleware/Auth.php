<?php

/**
 * @package KX
 * @subpackage Middleware\Auth
 */

declare(strict_types=1);

namespace KX\Middleware;

use KX\Core\Helper;
use KX\Core\Middleware;
use KX\Model\Users;
use KX\Model\UserRoles;
use KX\Model\Sessions;


use KX\Core\Request;
use KX\Core\Response;

final class Auth extends Middleware
{

    public function getSession(Request $request, Response $response)
    {
        global $kxAuthToken, $kxSession;

        if ($kxAuthToken && empty($kxSession)) {

            $output = (object)[];

            $sessionModel = new Sessions();
            $session = $sessionModel
                ->select('id, user_id, ip, header, last_act_on, last_act_at, expire_at')
                ->where('auth_token', $kxAuthToken)
                ->limit(1)
                ->get();

            if ($session && (empty($session->expire_at) || $session->expire_at > time())) {

                $output->session = $session;

                if ($session->user_id) {

                    $userModel = new Users();
                    $user = $userModel
                        ->select('id, u_name, f_name, l_name, email, email, role_id, b_date, status')
                        ->where('id', $session->user_id)
                        ->notWhere('status', 'deleted')
                        ->limit(1)
                        ->get();
                    if ($user) {
                        $output->user = $user;

                        $userRolesModel = new UserRoles();
                        $userRole = $userRolesModel
                            ->select('id, name, routes')
                            ->where('id', $user->role_id)->get();
                        if ($userRole) {
                            $output->role = $userRole;
                            $output->role->routes = explode(',', $userRole->routes);
                        }
                    } else {
                        $sessionModel
                            ->where('id', $session->id)
                            ->delete();
                        return [];
                    }
                }

                $updateSession = [];
                // disabled for heartbeat (keep session alive)
                if ($request->getUri() !== '/auth/heartbeat') {
                    $updateSession['last_act_at'] = time();

                    // update last act on if changed
                    if ($session->last_act_on !== $request->getUri()) {
                        $updateSession['last_act_on'] = $request->getUri();
                    }
                }


                // add 2 days to expire_at when 5 minutes left
                if (!empty($session->expire_at) && $session->expire_at - time() < 300) {
                    $updateSession['expire_at'] = strtotime('+2 days');
                }

                // update ip and header if changed
                if ($session->ip !== Helper::getIp()) {
                    $updateSession['ip'] = Helper::getIp();
                }

                if ($session->header !== Helper::getUserAgent()) {
                    $updateSession['header'] = Helper::getUserAgent();
                }

                if (!empty($updateSession)) {
                    $sessionModel
                        ->where('id', $session->id)
                        ->update($updateSession);
                }

                $session = $output;

                $kxSession = $session;
                return [
                    'session' => $session,
                    'authToken' => $kxAuthToken,
                ];
            }
        }

        return [];
    }

    public function isLogged(Request $request, Response $response)
    {
        $isLogged = $this->getSession($request, $response);
        if (!empty($isLogged)) {
            return $this->next($isLogged);
        } else {
            return $this->redirect('/auth/login', 301);
        }
    }

    public function isAuthorized(Request $request, Response $response, $instance)
    {
        $isLogged = $this->getSession($request, $response);
        $route = $request->getRouteDetails();
        $route = isset($route->route) === false ? $request->getUri() : $route->route;

        if (!empty($isLogged)) {
            if (isset($isLogged['session']->role) === false) {
                return $this->redirect('/auth/login', 301);
            } else {
                if (Helper::authorization($route)) {
                    return $this->next();
                } else {
                    return $this->redirect('/auth/login', 301);
                }
            }
        } else {
            return $this->redirect('/auth/login', 301);
        }
    }

    public function isNotLogged(Request $request, Response $response)
    {
        $isLogged = $this->getSession($request, $response);
        if (empty($isLogged)) {
            return $this->next();
        } else {
            return $this->redirect('/', 301);
        }
    }
}
