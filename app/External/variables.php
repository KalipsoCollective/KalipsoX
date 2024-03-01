<?php

use KX\Core\Helper;
use KX\Core\Model;

// get roles
$roleQuery = (new Model('user_roles'))
    ->select('id, name')
    ->table('user_roles')
    ->cache(60)
    ->getAll();

$roles = [];
if (!empty($roleQuery)) {

    foreach ($roleQuery as $role) {
        $roles[$role->id] = $role->name;
    }
}

$endpoints = require Helper::path('app/External/endpoints.php');

$userStatus = [
    'active' => Helper::lang('base.active'),
    'passive' => Helper::lang('base.passive'),
    'deleted' => Helper::lang('base.deleted'),
];

$languages = [
    'en' => 'English',
    'tr' => 'Türkçe',
];

return [
    'datatables' => [
        'tables' => [
            'users' => [
                'columns' => [
                    'id' => [
                        'name' => 'ID',
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'number',
                    ],
                    'u_name' => [
                        'name' => Helper::lang('auth.username'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'f_name' => [
                        'name' => Helper::lang('auth.first_name'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'l_name' => [
                        'name' => Helper::lang('auth.last_name'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'email' => [
                        'name' => Helper::lang('auth.email'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'role_id' => [
                        'name' => Helper::lang('auth.role'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'select',
                        'options' => $roles,
                        'formatter' => function ($d, $row) {
                            return $row['role'];
                        }
                    ],
                    'b_date' => [
                        'name' => Helper::lang('auth.birthdate'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'status' => [
                        'name' => Helper::lang('base.status'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'select',
                        'options' => $userStatus,
                        'formatter' => function ($d, $row) {
                            $return = '<span class="badge bg-';
                            if ($d === 'active') {
                                $return .= 'green';
                            } else if ($d === 'passive') {
                                $return .= 'yellow';
                            } else if ($d === 'deleted') {
                                $return .= 'red';
                            }
                            $return .= '-lt">' . Helper::lang('base.' . $d) . '</span>';
                            return $return;
                        }
                    ],
                    'created_at' => [
                        'name' => Helper::lang('base.created_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'updated_at' => [
                        'name' => Helper::lang('base.updated_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'actions' => [
                        'name' => Helper::lang('base.actions'),
                        'visible' => true,
                        'searchable' => false,
                        'orderable' => false,
                        'formatter' => function ($d, $row) {

                            $return = '';
                            if (Helper::authorization('dashboard/users/edit/:id')) {
                                $return .= '
                                    <a data-kx-action="' . Helper::base('dashboard/users/edit/' . $row['id']) . '" href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="' . Helper::lang('base.edit') . '">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                ';
                            }

                            if (
                                Helper::authorization('dashboard/users/delete/:id')
                            ) {
                                $return .= '
                                    <a data-kx-again ' . ((int)$row['id'] !== (int)Helper::sessionData('user', 'id') ? 'data-kx-action="' . Helper::base('dashboard/users/delete/' . $row['id']) . '" ' : 'disabled ') . 'href="javascript:;" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="' . Helper::lang('base.delete') . '">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                ';
                            }

                            return $return;
                        },
                    ],
                ],
                'external_columns' => [
                    'role'
                ],
                'order' => [
                    'name' => 'id',
                    'dir' => 'asc',
                ],
                'url' => Helper::base('dashboard/data/users'),
                'sql' => '
                    (SELECT 
                        u.id,
                        u.u_name,
                        u.f_name,
                        u.l_name,
                        u.email,
                        IFNULL(
                            FROM_UNIXTIME(u.b_date, "%Y-%m-%d"),
                        "-") as b_date,
                        u.status,
                        IFNULL(
                            FROM_UNIXTIME(u.created_at, "%Y-%m-%d %H:%i"),
                        "-") as created_at,
                        IFNULL(
                            FROM_UNIXTIME(u.updated_at, "%Y-%m-%d %H:%i"),
                        "-") as updated_at,
                        u.role_id,
                        (SELECT name FROM user_roles ur WHERE ur.id = u.role_id) as role
                    FROM users u
                ) as result',
                'modal' => function ($data = null, $onlyBody = false) use ($roles, $userStatus) {

                    $type = is_null($data) ? 'add' : 'edit';
                    $id = is_null($data) ? '' : $data->id;

                    $roleSelect = '';
                    foreach ($roles as $roleId => $roleName) {
                        $selected = false;
                        if ($type === 'edit' && isset($data->role_id) && $data->role_id === $roleId) {
                            $selected = true;
                        }
                        $roleSelect .= '<option value="' .  $roleId . '"' . ($selected ? ' selected' : '') . '>' . $roleName . '</option>';
                    }

                    $statusSelect = '';
                    foreach ($userStatus as $status => $name) {
                        if (Helper::sessionData('user', 'id') === $id && $status === 'deleted') {
                            continue;
                        }
                        $selected = false;
                        if ($type === 'edit' && isset($data->status) && $data->status === $status) {
                            $selected = true;
                        }
                        $statusSelect .= '<option value="' . $status . '"' . ($selected ? ' selected' : '') . '>' . $name . '</option>';
                    }

                    $return = '';
                    if (!$onlyBody) {
                        $return = '
                        <div class="modal modal-blur fade" id="addUserModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="addUserModalContent">';
                    }

                    $return .= '
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">' . Helper::lang('base.' . $type . '_user') . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Helper::lang('base.close') . '"></button>
                        </div>
                        <form data-kx-form action="' . Helper::base($type === 'add' ? 'dashboard/users/add' : 'dashboard/users/edit/' . $id) . '" method="post" autocomplete="off">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">' . Helper::lang('auth.first_name') . '</label>
                                            <input type="text" class="form-control" name="f_name"' . (isset($data->f_name) ? ' value="' . $data->f_name . '"' : '') . ' />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">' . Helper::lang('auth.last_name') . '</label>
                                            <input type="text" class="form-control" name="l_name"' . (isset($data->l_name) ? ' value="' . $data->l_name . '"' : '') . ' />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label required">' . Helper::lang('auth.email') . '</label>
                                            <input type="email" class="form-control" name="email" required' . (isset($data->email) ? ' value="' . $data->email . '"' : '') . ' />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label required">' . Helper::lang('auth.username') . '</label>
                                            <input type="text" class="form-control" name="u_name" required' . (isset($data->f_name) ? ' value="' . $data->u_name . '"' : '') . ' />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label' . ($type === 'add' ? ' required' : '') . '">' . Helper::lang('auth.password') . '</label>
                                            <input type="password" class="form-control" name="password"' . ($type === 'add' ? ' required' : '') . ' />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label required">' . Helper::lang('auth.role') . '</label>
                                            <select class="form-select" name="role_id" required>
                                                <option value="">' . Helper::lang('base.select') . '</option>
                                                ' . $roleSelect . '
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label required">' . Helper::lang('base.status') . '</label>
                                            <select class="form-select" name="status" required>
                                                <option value="">' . Helper::lang('base.select') . '</option>
                                                ' . $statusSelect . '
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="javascript:;" class="btn btn-ghost-secondary ms-auto" data-bs-dismiss="modal">
                                    ' . Helper::lang('base.cancel') . '
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <span class="btn-loader spinner-border spinner-border-sm text-light" role="status"></span>
                                    <span class="btn-text"><i class="ti ti-' . ($type === 'add' ? 'plus' : 'device-floppy') . ' icon"></i>' . Helper::lang('base.' . $type) . '</span>
                                </button>
                            </div>
                        </form>
                    </div>';

                    if (!$onlyBody) {
                        $return .= '
                            </div>
                        </div>
                        <div class="modal modal-blur fade" id="editUserModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="editUserModalContent">
                            </div>
                        </div>';
                    }
                    return $return;
                }
            ],
            'user-roles' => [
                'columns' => [
                    'id' => [
                        'name' => 'ID',
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'number',
                    ],
                    'name' => [
                        'name' => Helper::lang('base.name'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'user_count' => [
                        'name' => Helper::lang('base.total_users'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                        'formatter' => function ($d, $row) {
                            return '<kbd>' . $d . '</kbd>';
                        }
                    ],
                    'routes' => [
                        'name' => Helper::lang('base.routes'),
                        'visible' => true,
                        'searchable' => false,
                        'orderable' => false,
                        'type' => 'text',
                        'formatter' => function ($d, $row) {
                            $routes = explode(',', $row['routes']);
                            return '<kbd>' . count($routes) . '</kbd>';
                        }
                    ],
                    'created_at' => [
                        'name' => Helper::lang('base.created_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'updated_at' => [
                        'name' => Helper::lang('base.updated_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                        'type' => 'text',
                    ],
                    'actions' => [
                        'name' => Helper::lang('base.actions'),
                        'visible' => true,
                        'searchable' => false,
                        'orderable' => false,
                        'formatter' => function ($d, $row) {

                            $return = '';
                            if (Helper::authorization('dashboard/user-roles/edit/:id')) {
                                $return .= '
                                    <a data-kx-action="' . Helper::base('dashboard/user-roles/edit/' . $row['id']) . '" href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="' . Helper::lang('base.edit') . '">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                ';
                            }

                            if (
                                Helper::authorization('dashboard/user-roles/delete/:id')
                            ) {
                                $return .= '
                                    <a data-kx-again data-kx-action="' . Helper::base('dashboard/user-roles/delete/' . $row['id']) . '" href="javascript:;" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="' . Helper::lang('base.delete') . '">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                ';
                            }

                            return $return;
                        },
                    ],
                ],
                'external_columns' => [
                    // 'id'
                ],
                'order' => [
                    'name' => 'id',
                    'dir' => 'asc',
                ],
                'url' => Helper::base('dashboard/data/user-roles'),
                'sql' => '
                    (SELECT 
                        ur.id,
                        ur.name,
                        ur.routes,
                        IFNULL(
                            FROM_UNIXTIME(ur.created_at, "%Y-%m-%d %H:%i"),
                        "-") as created_at,
                        IFNULL(
                            FROM_UNIXTIME(ur.updated_at, "%Y-%m-%d %H:%i"),
                        "-") as updated_at,
                        (SELECT COUNT(id) FROM users u WHERE u.role_id = ur.id AND u.status != "deleted") as user_count
                    FROM user_roles ur
                ) as result',
                'modal' => function ($data = null, $onlyBody = false) use ($roles, $endpoints) {

                    $type = is_null($data) ? 'add' : 'edit';
                    $id = is_null($data) ? '' : $data->id;

                    $roleSelect = '';
                    foreach ($roles as $roleId => $roleName) {
                        if ($type === 'edit' && isset($data->id) && (int)$data->id === (int)$roleId) {
                            continue;
                        }
                        $roleSelect .= '<option value="' .  $roleId . '">' . $roleName . '</option>';
                    }

                    $endpointSelect = '';
                    foreach ($endpoints as $endpoint => $endpointDetails) {
                        $checked = false;
                        if ($endpointDetails['default']) {
                            $checked = true;
                        }

                        if ($type === 'edit') {
                            if (isset($data->routes) && in_array($endpoint, explode(',', $data->routes))) {
                                $checked = true;
                            } else {
                                $checked = false;
                            }
                        }

                        $endpointSelect .= '
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input name="routes[]" value="' . $endpoint . '" class="form-check-input" type="checkbox"' . ($checked ? ' checked' : '') . '>
                                    <span class="form-check-label">' . Helper::lang($endpointDetails['name']) . '</span>
                                </label>
                            </div>
                        </div>';
                    }

                    $return = '';
                    if (!$onlyBody) {
                        $return = '
                        <div class="modal modal-blur fade" id="addUserRoleModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="addUserRoleModalContent">';
                    }

                    $return .= '
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">' . Helper::lang('base.' . $type . '_user_role') . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Helper::lang('base.close') . '"></button>
                        </div>
                        <form data-kx-form action="' . Helper::base($type === 'add' ? 'dashboard/user-roles/add' : 'dashboard/user-roles/edit/' . $id) . '" method="post" autocomplete="off">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label required">' . Helper::lang('base.name') . '</label>
                                            <input type="text" class="form-control" name="name"' . (isset($data->name) ? ' value="' . $data->name . '"' : '') . ' required />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    ' . $endpointSelect . '
                                </div>
                            </div>
                            ' . ($type === 'edit' ? '
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="text-muted small">' . Helper::lang('base.user_role_transfer_note') . '</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">' . Helper::lang('auth.role') . '</label>
                                            <select class="form-select" name="role_id">
                                                <option value="">' . Helper::lang('base.select') . '</option>
                                                ' . $roleSelect . '
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ' : '') . '
                            <div class="modal-footer">
                                <a href="javascript:;" class="btn btn-ghost-secondary ms-auto" data-bs-dismiss="modal">
                                    ' . Helper::lang('base.cancel') . '
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <span class="btn-loader spinner-border spinner-border-sm text-light" role="status"></span>
                                    <span class="btn-text"><i class="ti ti-' . ($type === 'add' ? 'plus' : 'device-floppy') . ' icon"></i>' . Helper::lang('base.' . $type) . '</span>
                                </button>
                            </div>
                        </form>
                    </div>';

                    if (!$onlyBody) {
                        $return .= '
                            </div>
                        </div>
                        <div class="modal modal-blur fade" id="editUserRoleModal" tabindex="-1" data-bs-backdrop="static"">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="editUserRoleModalContent">
                            </div>
                        </div>';
                    }
                    return $return;
                }
            ]
        ],
        'default' => []
    ],
    'endpoints' => $endpoints,
    'settings' => [
        'name' => [
            'type' => 'text',
            'label' => Helper::lang('base.app_name'),
            'value' => Helper::config('settings.name'),
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'description' => [
            'type' => 'textarea',
            'label' => Helper::lang('base.description'),
            'value' => Helper::config('settings.description'),
            'required' => true,
            'multilanguage' => true,
            'col' => 'col-lg-6',
        ],
        'contact_email' => [
            'type' => 'email',
            'label' => Helper::lang('base.contact_email'),
            'value' => Helper::config('settings.contact_email'),
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'language' => [
            'type' => 'select',
            'label' => Helper::lang('base.default_language'),
            'value' => Helper::config('settings.language'),
            'options' => $languages,
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'default_user_role' => [
            'type' => 'select',
            'label' => Helper::lang('base.default_user_role'),
            'value' => Helper::config('settings.default_user_role'),
            'options' => $roles,
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'maintenance_mode' => [
            'type' => 'switch',
            'label' => Helper::lang('base.maintenance_mode'),
            'value' => Helper::config('settings.maintenance_mode'),
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'maintenance_mode_desc' => [
            'type' => 'textarea',
            'label' => Helper::lang('base.maintenance_mode_desc'),
            'value' => Helper::config('settings.maintenance_mode_desc'),
            'required' => true,
            'multilanguage' => true,
            'col' => 'col-lg-6',
        ],
        'mail_send_type' => [
            'type' => 'select',
            'label' => Helper::lang('base.mail_send_type'),
            'value' => Helper::config('settings.mail_send_type'),
            'options' => [
                'server' => Helper::lang('base.server'),
                'smtp' => 'SMTP',
            ],
            'required' => true,
            'col' => 'col-lg-6',
        ],
        'smtp_address' => [
            'type' => 'text',
            'label' => Helper::lang('base.smtp_address'),
            'value' => Helper::config('settings.smtp_address'),
            'col' => 'col-lg-6',
        ],
        'smtp_port' => [
            'type' => 'number',
            'label' => Helper::lang('base.smtp_port'),
            'value' => Helper::config('settings.smtp_port'),
            'col' => 'col-lg-6',
        ],
        'smtp_email_address' => [
            'type' => 'email',
            'label' => Helper::lang('base.smtp_email_address'),
            'value' => Helper::config('settings.smtp_email_address'),
            'col' => 'col-lg-6',
        ],
        'smtp_email_password' => [
            'type' => 'password',
            'label' => Helper::lang('base.smtp_email_password'),
            'value' => Helper::config('settings.smtp_email_password'),
            'col' => 'col-lg-6',
        ],
        'smtp_secure' => [
            'type' => 'select',
            'label' => Helper::lang('base.smtp_secure'),
            'value' => Helper::config('settings.smtp_secure'),
            'options' => [
                'tls' => 'TLS',
                'ssl' => 'SSL',
            ],
            'col' => 'col-lg-6',
        ],
        'db_cache' => [
            'type' => 'switch',
            'label' => Helper::lang('base.db_cache'),
            'value' => Helper::config('settings.db_cache'),
            'col' => 'col-lg-6',
        ],
        'route_cache' => [
            'type' => 'switch',
            'label' => Helper::lang('base.route_cache'),
            'value' => Helper::config('settings.route_cache'),
            'col' => 'col-lg-6',
        ],
        'registration_system' => [
            'type' => 'switch',
            'label' => Helper::lang('base.registration_system'),
            'value' => Helper::config('settings.registration_system'),
            'col' => 'col-lg-6',
        ],
    ],
    'settings_groups' => [
        'general' => [
            'name' => Helper::lang('base.general'),
            'icon' => 'ti ti-settings',
            'settings' => [
                'name',
                'description',
                'contact_email',
                'language',
                'default_user_role',
                'registration_system',
            ],
        ],
        'mail' => [
            'name' => Helper::lang('base.mail'),
            'icon' => 'ti ti-mail',
            'settings' => [
                'mail_send_type',
                'smtp_address',
                'smtp_port',
                'smtp_email_address',
                'smtp_email_password',
                'smtp_secure',
            ],
        ],
        'cache' => [
            'name' => Helper::lang('base.cache'),
            'icon' => 'ti ti-cloud-down',
            'settings' => [
                'db_cache',
                'route_cache',
            ],
        ],
        'maintenance' => [
            'name' => Helper::lang('base.maintenance_mode'),
            'icon' => 'ti ti-lock',
            'settings' => [
                'maintenance_mode',
                'maintenance_mode_desc',
            ],
        ],
    ],
];
