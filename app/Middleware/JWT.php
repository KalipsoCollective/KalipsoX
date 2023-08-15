<?php
/**
 * @package KX
 * @subpackage Middleware\JWT
 */

declare(strict_types=1);

namespace KX\Middleware;

use KX\Core\Helper;

class JWT extends \KX\Core\Middleware {
    
        public function __construct() {
            parent::__construct();
        }
    
        public function handle() {
            /*
            $token = $this->get('request')->getHeader('Authorization');
            if (empty($token)) {
                $this->response->json([
                    'code' => 401,
                    'msg' => 'token is empty'
                ]);
            }
            $token = explode(' ', $token);
            if (count($token) != 2) {
                $this->response->json([
                    'code' => 401,
                    'msg' => 'token is invalid'
                ]);
            }
            $token = $token[1];
            $jwt = Helper::jwt();
            $jwt->setToken($token);
            $jwt->verify();
            $this->request->set('jwt', $jwt);
            return true;
            */
        }

}   