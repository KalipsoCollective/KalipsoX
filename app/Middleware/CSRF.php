<?php
/**
 * @package KX
 * @subpackage Middleware\CSRF
 */

declare(strict_types=1);

namespace KX\Middleware;

use KX\Core\Helper;

class CSRF extends \KX\Core\Middleware {

    public $auth = false;

    public function validate() {

        if ($this->get('request')->method === 'POST') {

            if (isset($this->get('request')->params['_token']) === false) {

                return [
                    'status' => false,
                    'statusCode' => 401,
                    'next'   => false,
                    'arguments' => [
                        'title' => Helper::lang('err'),
                        'error' => '401',
                        'output' => Helper::lang('error.csrf_token_mismatch')
                    ]
                ];

            } elseif (! Helper::verifyCSRF($this->get('request')->params['_token'])) {

                return [
                    'status' => false,
                    'statusCode' => 401,
                    'next'   => false,
                    'arguments' => [
                        'title' => Helper::lang('err'),
                        'error' => '401',
                        'output' => Helper::lang('error.csrf_token_incorrect')
                    ]
                ];

            } else {

                return [
                    'status' => true,
                    'next'   => true
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