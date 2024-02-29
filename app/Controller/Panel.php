<?php

/**
 * @package KX
 * @subpackage Controller\Panel
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;
use KX\Core\Model;


use KX\Core\Request;
use KX\Core\Response;

use KX\Controller\Notification;

use KX\Helper\SSP;
use KX\Model\Users;

final class Panel
{

    public function dashboard(Request $request, Response $response)
    {
        return $response->render('panel/dashboard', [
            'title' => Helper::lang('base.dashboard'),
            'description' => Helper::lang('base.dashboard_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function settings(Request $request, Response $response)
    {
        return $response->render('panel/settings', [
            'title' => Helper::lang('base.settings'),
            'description' => Helper::lang('base.settings_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function users(Request $request, Response $response)
    {
        return $response->render('panel/users', [
            'title' => Helper::lang('base.users'),
            'description' => Helper::lang('base.users_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function userRoles(Request $request, Response $response)
    {
        return $response->render('panel/user_roles', [
            'title' => Helper::lang('base.user_roles'),
            'description' => Helper::lang('base.user_roles_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function tableData(Request $request, Response $response)
    {

        global $kxVariables;

        extract(Helper::input([
            'table' => 'nulled_text',
        ], $request->getRouteDetails()->attributes));

        if (empty($table)) {
            return $response->json(['error' => 'Table name is required']);
        } else if (!isset($kxVariables['datatables']['tables'][$table])) {
            return $response->json(['error' => 'Table not found']);
        } else {
            $tableDetails = $kxVariables['datatables']['tables'][$table];

            $columns = [];
            $i = 0;
            foreach ($tableDetails['columns'] as $tKey => $column) {
                if ($tKey === 'actions') {
                    $tKey = 'id';
                }

                $col = [
                    'db' => $tKey,
                    'dt' => $i,
                ];
                if (isset($column['formatter']) !== false) {
                    $col['formatter'] = $column['formatter'];
                }
                $columns[] = $col;
                $i++;
            }

            if (isset($tableDetails['external_columns']) !== false) {
                foreach ($tableDetails['external_columns'] as $column) {
                    $columns[] = [
                        'db' => $column,
                        'dt' => $i,
                    ];
                    $i++;
                }
            }

            $sqlSelect = isset($tableDetails['sql']) !== false ? $tableDetails['sql'] : '';

            $model = new Model();
            $ssp = SSP::simple_extend(
                $request->getRequestMethod() === 'POST' ? $_POST : $_GET,
                $model->pdo,
                isset($tableDetails['primaryKey']) !== false ? $tableDetails['primaryKey'] : 'id',
                $columns,
                $sqlSelect
            );

            return $response->json($ssp);
        }
    }

    public function userAdd(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'u_name' => 'nulled_text',
            'f_name' => 'nulled_text',
            'l_name' => 'nulled_text',
            'email' => 'nulled_text',
            'password' => 'nulled_text',
            'role_id' => 'int',
            'status' => 'nulled_text',
        ], $request->getParams()));

        $validation = Helper::validation([
            'u_name' => [
                'value' => $u_name,
                'pattern' => 'required|min:2|max:50|alphanumeric',
            ],
            'f_name' => [
                'value' => $f_name,
                'pattern' => 'required|min:2|max:50|alpha',
            ],
            'l_name' => [
                'value' => $l_name,
                'pattern' => 'required|min:2|max:50|alpha',
            ],
            'email' => [
                'value' => $email,
                'pattern' => 'required|email',
            ],
            'password' => [
                'value' => $password,
                'pattern' => 'required|min:6|max:50',
            ],
            'role_id' => [
                'value' => $role_id,
                'pattern' => 'required|numeric',
            ],
            'status' => [
                'value' => $status,
                'pattern' => 'required|in:active,passive,deleted',
            ]
        ]);

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
            $model = new Users();
            $checkEmail = $model->select('id')
                ->where('email', $email)
                ->get();

            if (!empty($checkEmail)) {
                $return['status'] = false;
                $return['dom'] = [
                    '[name="email"]' => [
                        'addClass' => 'is-invalid',
                    ],
                    '[name="email"] ~ .invalid-feedback' => [
                        'text' => Helper::lang('auth.email_already_exists')
                    ]
                ];
            } else {

                $checkUsername = $model->select('id')
                    ->where('u_name', $u_name)
                    ->get();

                if (!empty($checkUsername)) {
                    $return['status'] = false;
                    $return['dom'] = [
                        '[name="u_name"]' => [
                            'addClass' => 'is-invalid',
                        ],
                        '[name="u_name"] ~ .invalid-feedback' => [
                            'text' => Helper::lang('auth.username_already_exists')
                        ]
                    ];
                } else {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $data = ([
                        'u_name' => $u_name,
                        'f_name' => $f_name,
                        'l_name' => $l_name,
                        'email' => $email,
                        'password' => $password,
                        'role_id' => $role_id,
                        'status' => $status,
                        'token' => Helper::tokenGenerator(80)
                    ]);

                    $insert = $model->insert($data);

                    if ($insert) {

                        if ($status === 'passive') {
                            $data['id'] = $insert;
                            $notificationController = new Notification();
                            $notificationController->createNotification('welcome', $data);
                        }

                        $return['notify'][] = [
                            'type' => 'success',
                            'message' => Helper::lang('base.record_successfully_added')
                        ];

                        $return['form_reset'] = true;
                        $return['modal_hide'] = '#addUserModal';
                        $return['table_reload'] = 'users';
                    } else {
                        $return['status'] = false;
                        $return['notify'][] = [
                            'type' => 'error',
                            'message' => Helper::lang('auth.a_problem_has_occurred')
                        ];
                    }
                }
            }
        }

        return $response->json($return);
    }
}
