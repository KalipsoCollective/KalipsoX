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

    public $authToken;
    public $session;

    public function getSession(Request $request, Response $response)
    {
        if (Helper::config('AUTH_STRATEGY') === 'session') {
            $authToken = isset($_COOKIE[Helper::config('AUTH_COOKIE_NAME')]) !== false ?
                $_COOKIE[Helper::config('AUTH_COOKIE_NAME')] :
                null;
        } else {
            $authToken = $request->getHeader('Authorization');
        }

        if ($authToken) {
            $session = new Sessions();
            $session->where('auth_token', $authToken);
            $session->grouped(function ($session) {
                $session
                    ->orWhere('expire_at', '>', date('Y-m-d H:i:s'))
                    ->orWhere('expire_at', null);
            });
            $session->limit(1);
            $session = $session->get();
            if ($session) {
                $this->authToken = $authToken;
                $this->session = $session;
                $this->next([
                    'authToken' => $authToken,
                    'session' => $session
                ]);
                return [
                    'authToken' => $authToken,
                    'session' => $session
                ];
            }
        }

        return [];
    }

    public function isLogged(Request $request, Response $response)
    {
        $isLogged = $this->getSession($request, $response);
        if (!empty($isLogged)) {
            return $this->next();
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
