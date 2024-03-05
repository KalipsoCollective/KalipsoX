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
use KX\Helper\HTML;
use KX\Helper\SSP;
use KX\Model\Sessions;
use KX\Model\Users;
use KX\Model\UserRoles;

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

        if ($request->getRequestMethod() === 'POST' && $request->getHeader('Accept') === 'application/json') {

            global $kxVariables, $kxAvailableLanguages, $kxLang;

            $return = [
                'status' => true,
                'notify' => [],
            ];

            // set up the settings
            $currSettings = $kxVariables['settings'];

            extract(Helper::input([
                'settings' => 'nulled_text',
            ], $request->getParams()));

            // group the settings
            $currentSettings = [];
            foreach ($currSettings as $settingName => $settingDetails) {
                // get current setting
                $c = Helper::config('settings.' . $settingName, true);
                if (isset($settingDetails['multilanguage']) !== false && $settingDetails['multilanguage'] === true) {
                    $c = json_decode((string)$c, true);
                }

                // update with the new settings
                if (isset($settings[$settingName]) !== false) {
                    $currentSettings[$settingName] = $settings[$settingName];
                } else {
                    $currentSettings[$settingName] = $c;
                }

                // fix for switch
                if ($settingDetails['type'] === 'switch') {
                    if (in_array($currentSettings[$settingName], ['on', 'off'])) {
                        $currentSettings[$settingName] = $currentSettings[$settingName] === 'on';
                    }
                }

                // fix for multilanguage
                if (isset($settingDetails['multilanguage']) !== false && $settingDetails['multilanguage'] === true) {
                    $currentSettings[$settingName] = json_encode($currentSettings[$settingName]);
                }

                // fix for numeric
                if (is_numeric($currentSettings[$settingName])) {
                    $currentSettings[$settingName] = (float)$currentSettings[$settingName];
                }
            }

            $currentSettings['last_updated_at'] = time();
            $currentSettings['last_updated_by'] = Helper::sessionData('user', 'id');

            // save the settings as php string
            $settingsStr = '<?php ' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL;
            foreach ($currentSettings as $settingName => $settingValue) {
                $settingsStr .= '   \'' . $settingName . '\' => ';
                if (is_string($settingValue)) {
                    $settingsStr .= '\'' . str_replace('\'', '\\\'', $settingValue) . '\',' . PHP_EOL;
                } elseif (is_bool($settingValue)) {
                    $settingsStr .= ($settingValue ? 'true,' : 'false,') . PHP_EOL;
                } else {
                    $settingsStr .= $settingValue . ',' . PHP_EOL;
                }
            }
            $settingsStr .= '];';

            $settingsFile = Helper::path('app/Config/settings.php');
            $save = file_put_contents($settingsFile, $settingsStr);

            if ($save) {
                $return['notify'][] = [
                    'type' => 'success',
                    'message' => Helper::lang('base.settings_successfully_updated'),
                ];
                $return['dom'] = [
                    '.settings-card .timeago' => [
                        'attr' => [
                            'datetime' => date('c', $currentSettings['last_updated_at'])
                        ]
                    ]
                ];
            } else {
                $return['status'] = false;
                $return['notify'][] = [
                    'type' => 'error',
                    'message' => Helper::lang('base.settings_could_not_be_updated')
                ];
            }

            return $response->json($return);
        }



        return $response->render('panel/settings', [
            'title' => Helper::lang('base.settings'),
            'description' => Helper::lang('base.settings_desc'),
            'auth' => $request->getMiddlewareParams(),
            'settingsCard' => HTML::settingsCard(),
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

    public function tableData(Request $request, Response $response, $instance)
    {

        global $kxVariables;

        extract(Helper::input([
            'table' => 'nulled_text',
        ], $request->getRouteDetails()->attributes));
        $instance->setLogRecord(false);

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

        Helper::validation([
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
        ], $response);

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

        return $response->json($return);
    }

    public function userEdit(Request $request, Response $response)
    {

        global $kxVariables;
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'u_name' => 'nulled_text',
            'f_name' => 'nulled_text',
            'l_name' => 'nulled_text',
            'email' => 'nulled_text',
            'role_id' => 'int',
            'status' => 'nulled_text',
            'password' => 'nulled_text',
        ], $request->getParams()));

        extract(Helper::input([
            'id' => 'int',
        ], $request->getRouteDetails()->attributes));

        $getUser = (new Users())->select('
                id,
                u_name,
                f_name,
                l_name,
                email,
                role_id,
                status
            ')
            ->where('id', $id)
            ->get();

        if (empty($getUser)) {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.user_not_found')
            ];
        } else {

            if (empty($u_name) || empty($email) || empty($role_id) || empty($status)) { // prepare form

                $formContent = HTML::adminModalContents('users', $getUser, true);

                $return['status'] = true;
                $return['dom'] = [
                    '#editUserModalContent' => [
                        'html' => $formContent
                    ]
                ];
                $return['modal_show'] = '#editUserModal';
            } else {

                $v = [
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
                    'role_id' => [
                        'value' => $role_id,
                        'pattern' => 'required|numeric',
                    ],
                    'status' => [
                        'value' => $status,
                        'pattern' => 'required|in:active,passive,deleted',
                    ],
                    'password' => [
                        'value' => $password,
                        'pattern' => 'min:6|max:50',
                    ],
                ];
                if (empty($password)) {
                    unset($v['password']);
                }

                Helper::validation($v, $response);


                $model = new Users();
                $checkEmail = $model->select('id')
                    ->where('email', $email)
                    ->notWhere('id', $id)
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
                        ->notWhere('id', $id)
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
                        $data = ([
                            'u_name' => $u_name,
                            'f_name' => $f_name,
                            'l_name' => $l_name,
                            'email' => $email,
                            'role_id' => $role_id,
                            'status' => $status,
                        ]);

                        if (!empty($password)) {
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $data['password'] = $password;
                        }

                        $update = $model
                            ->where('id', $id)
                            ->update($data, $id);

                        if ($update) {
                            $return['notify'][] = [
                                'type' => 'success',
                                'message' => Helper::lang('base.record_successfully_updated')
                            ];

                            $return['form_reset'] = true;
                            $return['modal_hide'] = '#editUserModal';
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
        }

        return $response->json($return);
    }

    public function userDelete(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'id' => 'int',
        ], $request->getRouteDetails()->attributes));

        if ($id === Helper::sessionData('user', 'id')) {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.you_cannot_delete_yourself')
            ];
            return $response->json($return);
        }

        $model = new Users();
        $delete = $model
            ->where('id', $id)
            ->update(['status' => 'deleted']);

        if ($delete) {

            $sessionModel = new Sessions();
            $sessionModel
                ->where('user_id', $id)
                ->delete();

            $return['notify'][] = [
                'type' => 'success',
                'message' => Helper::lang('base.record_successfully_deleted')
            ];
            $return['table_reload'] = 'users';
        } else {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.a_problem_has_occurred')
            ];
        }

        return $response->json($return);
    }

    public function userRoleAdd(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'name' => 'nulled_text',
            'routes' => 'nulled_text',
        ], $request->getParams()));

        Helper::validation([
            'name' => [
                'value' => $name,
                'pattern' => 'required|min:2|max:50|alphanumeric',
            ],
        ], $response);

        if (empty($routes)) {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.please_select_at_least_one_route')
            ];
            return $response->json($return);
        } else {
            $routes = is_array($routes) ? implode(',', $routes) : $routes;
        }

        $model = new UserRoles();
        $checkName = $model->select('id')
            ->where('name', $name)
            ->get();

        if (!empty($checkName)) {
            $return['status'] = false;
            $return['dom'] = [
                '[name="name"]' => [
                    'addClass' => 'is-invalid',
                ],
                '[name="name"] ~ .invalid-feedback' => [
                    'text' => Helper::lang('auth.role_name_already_exists')
                ]
            ];
        } else {
            $data = ([
                'name' => $name,
                'routes' => $routes,
            ]);

            $insert = $model->insert($data);

            if ($insert) {
                $return['notify'][] = [
                    'type' => 'success',
                    'message' => Helper::lang('base.record_successfully_added')
                ];

                $return['form_reset'] = true;
                $return['modal_hide'] = '#addUserRoleModal';
                $return['table_reload'] = 'user-roles';
            } else {
                $return['status'] = false;
                $return['notify'][] = [
                    'type' => 'error',
                    'message' => Helper::lang('auth.a_problem_has_occurred')
                ];
            }
        }

        return $response->json($return);
    }

    public function userRoleEdit(Request $request, Response $response)
    {
        global $kxVariables;
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'name' => 'nulled_text',
            'routes' => 'nulled_text',
            'role_id' => 'int',
        ], $request->getParams()));

        extract(Helper::input([
            'id' => 'int',
        ], $request->getRouteDetails()->attributes));

        $getUserRole = (new UserRoles())->select('
                id,
                name,
                routes,
                (SELECT COUNT(id) FROM users WHERE role_id = user_roles.id) as user_count
            ')
            ->where('id', $id)
            ->get();

        if (empty($getUserRole)) {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.role_not_found')
            ];
        } else {

            if (empty($name) || empty($routes)) { // prepare form

                $formContent =
                    HTML::adminModalContents('user-roles', $getUserRole, true);

                $return['status'] = true;
                $return['dom'] = [
                    '#editUserRoleModalContent' => [
                        'html' => $formContent
                    ]
                ];
                $return['modal_show'] = '#editUserRoleModal';
            } else {

                $v = [
                    'name' => [
                        'value' => $name,
                        'pattern' => 'required|min:2|max:50|alphanumeric',
                    ],
                ];

                Helper::validation($v, $response);

                $model = new UserRoles();
                $checkName = $model->select('id')
                    ->where('name', $name)
                    ->notWhere('id', $id)
                    ->get();

                if (!empty($checkName)) {
                    $return['status'] = false;
                    $return['dom'] = [
                        '[name="name"]' => [
                            'addClass' => 'is-invalid',
                        ],
                        '[name="name"] ~ .invalid-feedback' => [
                            'text' => Helper::lang('auth.role_name_already_exists')
                        ]
                    ];
                } else {
                    $routes = is_array($routes) ? implode(',', $routes) : $routes;
                    $data = ([
                        'name' => $name,
                        'routes' => $routes,
                    ]);

                    $update = $model
                        ->where('id', $id)
                        ->update($data, $id);

                    if ($update) {
                        $return['notify'][] = [
                            'type' => 'success',
                            'message' => Helper::lang('base.record_successfully_updated')
                        ];

                        $return['form_reset'] = true;
                        $return['modal_hide'] = '#editUserRoleModal';
                        $return['table_reload'] = 'user-roles';

                        // transfer users to new role
                        if ($getUserRole->user_count > 0 && $role_id !== $getUserRole->id && $role_id) {
                            (new Users())
                                ->where('role_id', $id)
                                ->update(['role_id' => $role_id]);
                        }
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

    public function userRoleDelete(Request $request, Response $response)
    {
        $return = [
            'status' => true,
            'notify' => [],
        ];

        extract(Helper::input([
            'id' => 'int',
        ], $request->getRouteDetails()->attributes));

        $model = new UserRoles();
        $delete = $model
            ->where('id', $id)
            ->delete();

        if ($delete) {
            $return['notify'][] = [
                'type' => 'success',
                'message' => Helper::lang('base.record_successfully_deleted')
            ];
            $return['table_reload'] = 'user-roles';
        } else {
            $return['status'] = false;
            $return['notify'][] = [
                'type' => 'error',
                'message' => Helper::lang('auth.a_problem_has_occurred')
            ];
        }

        return $response->json($return);
    }
}
