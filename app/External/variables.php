<?php

use KX\Core\Helper;
use KX\Core\Model;

$baseModel = new Model();

/**
 * ENDPOINTS
 * We use this definition for endpoint definitions for display.
 * ex: [endpoint] => ['default' => (checked status), 'name' => (language definition for display)]
 */
$endpoints = [
    // Dashboard
    'dashboard' => [
        'default' => true,
        'name' => 'authorization.dashboard',
        'group' => 'basic',
    ],
    'dashboard.data.:table' => [
        'default' => false,
        'name' => 'authorization.table_data',
        'group' => 'advanced',
    ],
    'dashboard.settings' => [
        'default' => false,
        'name' => 'authorization.settings',
        'group' => 'advanced',
    ],
    // Users
    'dashboard.users' => [
        'default' => false,
        'name' => 'authorization.users',
        'group' => 'user_module',

    ],
    'dashboard.users.add' => [
        'default' => false,
        'name' => 'authorization.users_add',
        'group' => 'user_module',
    ],
    'dashboard.users.edit.:id' => [
        'default' => false,
        'name' => 'authorization.users_edit',
        'group' => 'user_module',
    ],
    'dashboard.users.delete.:id' => [
        'default' => false,
        'name' => 'authorization.users_delete',
        'group' => 'user_module',
    ],
    // User Roles
    'dashboard.user_roles' => [
        'default' => false,
        'name' => 'authorization.user_roles',
        'group' => 'user_roles_module',
    ],
    'dashboard.user_roles.add' => [
        'default' => false,
        'name' => 'authorization.user_roles_add',
        'group' => 'user_roles_module',
    ],
    'dashboard.user_roles.edit.:id' => [
        'default' => false,
        'name' => 'authorization.user_roles_edit',
        'group' => 'user_roles_module',
    ],
    'dashboard.user_roles.delete.:id' => [
        'default' => false,
        'name' => 'authorization.user_roles_delete',
        'group' => 'user_roles_module',
    ],
    // Sessions
    'dashboard.sessions' => [
        'default' => false,
        'name' => 'authorization.sessions',
        'group' => 'session_module',
    ],
    'dashboard.sessions.delete.:id' => [
        'default' => false,
        'name' => 'authorization.sessions_delete',
        'group' => 'session_module',
    ],
    // Modules
    'dashboard.modules' => [
        'default' => false,
        'name' => 'authorization.modules',
        'group' => 'system_module',
    ],
    'dashboard.modules.add' => [
        'default' => false,
        'name' => 'authorization.modules_add',
        'group' => 'system_module',
    ],
    'dashboard.modules.edit.:id' => [
        'default' => false,
        'name' => 'authorization.modules_edit',
        'group' => 'system_module',
    ],
    'dashboard.modules.delete.:id' => [
        'default' => false,
        'name' => 'authorization.modules_delete',
        'group' => 'system_module',
    ],
    // Widgets
    'dashboard.widgets' => [
        'default' => false,
        'name' => 'authorization.widgets',
        'group' => 'system_module',
    ],
    'dashboard.widgets.add' => [
        'default' => false,
        'name' => 'authorization.widgets_add',
        'group' => 'system_module',
    ],
    'dashboard.widgets.edit.:id' => [
        'default' => false,
        'name' => 'authorization.widgets_edit',
        'group' => 'system_module',
    ],
    'dashboard.widgets.delete.:id' => [
        'default' => false,
        'name' => 'authorization.widgets_delete',
        'group' => 'system_module',
    ],
    // Languages
    'dashboard.languages' => [
        'default' => false,
        'name' => 'authorization.languages',
        'group' => 'system_module',
    ],
    'dashboard.languages.save' => [
        'default' => false,
        'name' => 'authorization.languages_save',
        'group' => 'system_module',
    ],
    // Logs
    'dashboard.logs' => [
        'default' => false,
        'name' => 'authorization.logs',
        'group' => 'report_module',
    ],
];

/**
 * ROLE & ROLE GROUPS
 * We use this definition for role definitions for display.
 */
if ($baseModel->pdo->query('SHOW TABLES LIKE "user_roles"')->rowCount()) {

    $_roles = ($baseModel)
        ->select('id, name')
        ->table('user_roles')
        ->cache(60)
        ->getAll();
} else {
    $_roles = [];
}

$roles = [];
if (!empty($_roles)) {
    // groupping
    foreach ($_roles as $role) {
        $roles[$role->id] = $role->name;
    }
}
$roleGroups = [
    'basic' => [
        'name' => Helper::lang('authorization.basic'),
        'icon' => 'ti ti-route',
    ],
    'user_module' => [
        'name' => Helper::lang('authorization.user_module'),
        'icon' => 'ti ti-user',
    ],
    'user_roles_module' => [
        'name' => Helper::lang('authorization.user_roles_module'),
        'icon' => 'ti ti-lock',
    ],
    'session_module' => [
        'name' => Helper::lang('authorization.session_module'),
        'icon' => 'ti ti-fingerprint',
    ],
    'advanced' => [
        'name' => Helper::lang('authorization.advanced'),
        'icon' => 'ti ti-shield-cog',
    ],
    'report_module' => [
        'name' => Helper::lang('authorization.report_module'),
        'icon' => 'ti ti-report',
    ],
    'system_module' => [
        'name' => Helper::lang('authorization.system_module'),
        'icon' => 'ti ti-package',
    ],
];

/**
 * USER STATUS
 * We use this definition for user status definitions for display.
 */
$userStatus = [
    'active' => Helper::lang('base.active'),
    'passive' => Helper::lang('base.passive'),
    'deleted' => Helper::lang('base.deleted'),
];

/**
 * LANGUAGES
 * We use this definition for language definitions for display.
 */
global $kxAvailableLanguages;
$languages = [];
foreach ($kxAvailableLanguages as $lang) {
    $languages[$lang] = Helper::lang('langs.' . $lang);
}

/**
 * SETTINGS & SETTINGS GROUPS
 * We use this definition for settings definitions and settings groups for display.
 */

$settings = [
    'name' => [
        'type' => 'text',
        'label' => Helper::lang('base.app_name'),
        'value' => Helper::config('settings.name'),
        'required' => true,
        // 'attributes' => ' minlength="3" maxlength="50"',
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
];

$settingsGroups = [
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
];

/**
 * DATATABLES
 * We use this definition for datatable definitions.
 */
$dataTables = [
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
                'activity_status' => [
                    'name' => Helper::lang('base.activity_status'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => false,
                    'type' => 'select',
                    'options' => [
                        'online' => Helper::lang('base.online'),
                        'offline' => Helper::lang('base.offline'),
                    ],
                    'formatter' => function ($d, $row) {
                        $isOnline = $d === 'online';
                        return '
                        <span class="text-nowrap small" data-bs-toggle="tooltip" title="' . Helper::lang('base.' . ($isOnline ? 'online' : 'offline')) . '">
                            <span class="badge' . ($isOnline ? ' bg-green text-green-fg' : ' bg-red text-red-fg') . ' tag-status badge-empty"></span> 
                            <time' . (!empty($row['activity_date']) ? ' class="timeago" datetime="' . date('c', (int)$row['activity_date']) . '"' : '') . '>
                                ' . (!empty($row['activity_date']) ? date('Y-m-d H:i', (int)$row['activity_date']) : '-') . '
                            </time>
                        </span>';
                    }
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
                'role',
                'activity_status',
                'activity_date'
            ],
            'order' => [
                'name' => 'id',
                'dir' => 'asc',
            ],
            'url' => Helper::base('dashboard/data/users'),
            'sql' => 'SELECT 
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
                    (SELECT name FROM user_roles ur WHERE ur.id = u.role_id) as role,
                    (SELECT last_act_at FROM sessions s WHERE s.user_id = u.id ORDER BY last_act_at DESC LIMIT 1) as activity_date,
                    IF(
                        (SELECT last_act_at FROM sessions s WHERE s.user_id = u.id ORDER BY last_act_at DESC LIMIT 1) < UNIX_TIMESTAMP() - 300,
                        "offline",
                        "online"
                    ) as activity_status
                FROM users u',
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
            'sql' => 'SELECT 
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
                FROM user_roles ur',
        ],
        'sessions' => [
            'columns' => [
                'id' => [
                    'name' => 'ID',
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'number',
                ],
                'user' => [
                    'name' => Helper::lang('base.user'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {

                        return '<kbd>#' . $row['user_id'] . '</kbd> ' . $d;
                    }
                ],
                'auth_token' => [
                    'name' => Helper::lang('base.auth_token'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'ip' => [
                    'name' => Helper::lang('base.ip_address'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'header' => [
                    'name' => Helper::lang('base.device'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => false,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $deviceInfo = Helper::userAgentDetails($d);
                        return '<p class="mb-0 text-nowrap" title="' . $deviceInfo['user_agent'] . '">
                            <i class="ti ti-' . $deviceInfo['p_icon'] . '"></i> ' . $deviceInfo['platform'] . ' — 
                            <i class="ti ti-' . $deviceInfo['b_icon'] . '"></i> ' . $deviceInfo['browser'] . ' ' . $deviceInfo['version'] . '
                        </p>';
                    }
                ],
                'last_act_on' => [
                    'name' => Helper::lang('base.last_activity'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                ],
                'last_act_at_readable' => [
                    'name' => Helper::lang('base.last_activity_at'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $isOnline = $row['last_act_at'] > strtotime('-5 minutes');
                        return '
                        <span class="text-nowrap small" data-bs-toggle="tooltip" title="' . Helper::lang('base.' . ($isOnline ? 'online' : 'offline')) . '">
                            <span class="badge' . ($isOnline ? ' bg-green text-green-fg' : ' bg-red text-red-fg') . ' tag-status badge-empty"></span> ' . $d . '
                        </span>';
                    }
                ],
                'created_at' => [
                    'name' => Helper::lang('base.created_at'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                ],
                'expire_at' => [
                    'name' => Helper::lang('base.expire_at'),
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

                        global $kxAuthToken;

                        if ($row['auth_token'] === $kxAuthToken) {
                            return '';
                        }

                        $return = '';
                        if (Helper::authorization('dashboard/sessions/delete/:id')) {
                            $return .= '
                                <a data-kx-again data-kx-action="' . Helper::base('dashboard/sessions/delete/' . $row['id']) . '" href="javascript:;" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="' . Helper::lang('base.delete_session') . '">
                                    <i class="ti ti-fingerprint-off"></i>
                                </a>
                            ';
                        }

                        return $return;
                    },
                ],
            ],
            'external_columns' => [
                'last_act_at',
                'user_id'
            ],
            'order' => [
                'name' => 'id',
                'dir' => 'asc',
            ],
            'url' => Helper::base('dashboard/data/sessions'),
            'sql' => 'SELECT 
                    s.id,
                    s.user_id,
                    (SELECT u.u_name FROM users u WHERE u.id = s.user_id) as user,
                    s.auth_token,
                    s.ip,
                    s.header,
                    s.last_act_on,
                    s.last_act_at,
                    IFNULL(
                        FROM_UNIXTIME(s.last_act_at, "%Y-%m-%d %H:%i"),
                    "-") as last_act_at_readable,
                    IFNULL(
                        FROM_UNIXTIME(s.expire_at, "%Y-%m-%d %H:%i"),
                    "-") as expire_at,
                    IFNULL(
                        FROM_UNIXTIME(s.created_at, "%Y-%m-%d %H:%i"),
                    "-") as created_at
                FROM sessions s',
        ],
        'logs' => [
            'columns' => [
                'id' => [
                    'name' => 'ID',
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'number',
                ],
                'user' => [
                    'name' => Helper::lang('base.user'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {

                        return !empty($row['created_by']) ? '<kbd>#' . $row['created_by'] . '</kbd> ' . $d : '-';
                    }
                ],
                'auth_token' => [
                    'name' => Helper::lang('base.auth_token'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'endpoint' => [
                    'name' => Helper::lang('base.endpoint'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'status_code' => [
                    'name' => Helper::lang('base.status_code'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $d = (int)$d;
                        return '<span class="badge bg-' . ($d >= 200 && $d < 300 ? 'green' : ($d >= 300 && $d < 400 ? 'yellow' : 'red')) . '-lt">' . $d . '</span>';
                    }
                ],
                'method' => [
                    'name' => Helper::lang('base.method'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<code>' . $d . '</code>';
                    }
                ],
                'ip' => [
                    'name' => Helper::lang('base.ip_address'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'header' => [
                    'name' => Helper::lang('base.device'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => false,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $deviceInfo = Helper::userAgentDetails($d);
                        return '<p class="mb-0 text-nowrap" title="' . $deviceInfo['user_agent'] . '">
                            <i class="ti ti-' . $deviceInfo['p_icon'] . '"></i> ' . $deviceInfo['platform'] . ' — 
                            <i class="ti ti-' . $deviceInfo['b_icon'] . '"></i> ' . $deviceInfo['browser'] . ' ' . $deviceInfo['version'] . '
                        </p>';
                    }
                ],
                'exec_time' => [
                    'name' => Helper::lang('base.exec_time'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<code>' . $d . 'ms</code>';
                    }
                ],
                'created_at' => [
                    'name' => Helper::lang('base.created_at'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                ],
                /*
                'actions' => [
                    'name' => Helper::lang('base.actions'),
                    'visible' => true,
                    'searchable' => false,
                    'orderable' => false,
                    'formatter' => function ($d, $row) {


                    },
                ], */
            ],
            'external_columns' => [
                'created_by'
            ],
            'order' => [
                'name' => 'id',
                'dir' => 'asc',
            ],
            'url' => Helper::base('dashboard/data/logs'),
            'sql' => 'SELECT 
                    l.id,
                    l.created_by,
                    (SELECT u.u_name FROM users u WHERE u.id = l.created_by) as user,
                    l.endpoint,
                    l.auth_token,
                    l.status_code,
                    l.ip,
                    l.header,
                    l.exec_time,
                    l.method,
                    IFNULL(
                        FROM_UNIXTIME(l.created_at, "%Y-%m-%d %H:%i"),
                    "-") as created_at
                FROM logs l',
        ],
        'modules' => [
            'columns' => [
                'id' => [
                    'name' => 'ID',
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'number',
                ],
                'key' => [
                    'name' => Helper::lang('base.key'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {

                        return '<kbd>#' . $row['key'] . '</kbd>';
                    }
                ],
                'auth_token' => [
                    'name' => Helper::lang('base.auth_token'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'endpoint' => [
                    'name' => Helper::lang('base.endpoint'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'status_code' => [
                    'name' => Helper::lang('base.status_code'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $d = (int)$d;
                        return '<span class="badge bg-' . ($d >= 200 && $d < 300 ? 'green' : ($d >= 300 && $d < 400 ? 'yellow' : 'red')) . '-lt">' . $d . '</span>';
                    }
                ],
                'method' => [
                    'name' => Helper::lang('base.method'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<code>' . $d . '</code>';
                    }
                ],
                'ip' => [
                    'name' => Helper::lang('base.ip_address'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<kbd>' . $d . '</kbd>';
                    }
                ],
                'header' => [
                    'name' => Helper::lang('base.device'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => false,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        $deviceInfo = Helper::userAgentDetails($d);
                        return '<p class="mb-0 text-nowrap" title="' . $deviceInfo['user_agent'] . '">
                            <i class="ti ti-' . $deviceInfo['p_icon'] . '"></i> ' . $deviceInfo['platform'] . ' — 
                            <i class="ti ti-' . $deviceInfo['b_icon'] . '"></i> ' . $deviceInfo['browser'] . ' ' . $deviceInfo['version'] . '
                        </p>';
                    }
                ],
                'exec_time' => [
                    'name' => Helper::lang('base.exec_time'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                    'formatter' => function ($d, $row) {
                        return '<code>' . $d . 'ms</code>';
                    }
                ],
                'created_at' => [
                    'name' => Helper::lang('base.created_at'),
                    'visible' => true,
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'text',
                ],
                /*
                'actions' => [
                    'name' => Helper::lang('base.actions'),
                    'visible' => true,
                    'searchable' => false,
                    'orderable' => false,
                    'formatter' => function ($d, $row) {


                    },
                ], */
            ],
            'external_columns' => [
                'created_by'
            ],
            'order' => [
                'name' => 'id',
                'dir' => 'asc',
            ],
            'url' => Helper::base('dashboard/data/modules'),
            'sql' => 'SELECT 
                    l.id,
                    l.created_by,
                    (SELECT u.u_name FROM users u WHERE u.id = l.created_by) as user,
                    l.endpoint,
                    l.auth_token,
                    l.status_code,
                    l.ip,
                    l.header,
                    l.exec_time,
                    l.method,
                    IFNULL(
                        FROM_UNIXTIME(l.created_at, "%Y-%m-%d %H:%i"),
                    "-") as created_at
                FROM logs l',
        ],
    ],
    'default' => []
];


/**
 * NOTIFICATIONS
 * We use this definition for notification messages and email templates.
 */
$notifications = [
    // User Registration Notification
    'welcome' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.welcome_title',
                'body' => 'notification.welcome_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.welcome_email_title'),
                'body' => Helper::lang('notification.welcome_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/verify-account?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.verify_account'),
                ]),
            ]
        ];
    },
    'account_verified' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.account_verified_title',
                'body' => 'notification.account_verified_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.account_verified_email_title'),
                'body' => Helper::lang('notification.account_verified_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/login'),
                    'link_text' => Helper::lang('auth.login'),
                ]),
            ]
        ];
    },
    'recovery_request' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.recovery_request_title',
                'body' => 'notification.recovery_request_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.recovery_request_email_title'),
                'body' => Helper::lang('notification.recovery_request_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/recovery?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.recovery_account'),
                ]),
            ]
        ];
    },
    'recover_success' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.recover_success_title',
                'body' => 'notification.recover_success_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.recover_success_email_title'),
                'body' => Helper::lang('notification.recover_success_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/login'),
                    'link_text' => Helper::lang('auth.login'),
                ]),
            ]
        ];
    },
    'email_change' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.email_change_title',
                'body' => 'notification.email_change_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.email_change_email_title'),
                'body' => Helper::lang('notification.email_change_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/verify-account?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.verify_account'),
                ]),
            ]
        ];
    },
];

/**
 * SIDEBAR ROUTES
 * We use this definition for sidebar menu items.
 */
$sidebarRoutes = [
    'dashboard' => [
        'icon' => 'ti ti-layout-dashboard',
        'link' => '/dashboard',
        // 'badge' => '<span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span>'
    ],
    'users' => [
        'icon' => 'ti ti-users',
        'children' => [
            'users' => [
                'link' => '/dashboard/users',
            ],
            'user_roles' => [
                'link' => '/dashboard/user-roles',
            ],
            'sessions' => [
                'link' => '/dashboard/sessions',
            ],
        ],
    ],
    'settings' => [
        'icon' => 'ti ti-settings',
        'link' => '/dashboard/settings',
    ],
    'system' => [
        'icon' => 'ti ti-package',
        'children' => [
            'modules' => [
                'link' => '/dashboard/modules',
            ],
            'widgets' => [
                'link' => '/dashboard/widgets',
            ],
            'languages' => [
                'link' => '/dashboard/languages',
            ],
        ]
    ],
    'reports' => [
        'icon' => 'ti ti-report',
        'children' => [
            'logs' => [
                'link' => '/dashboard/logs',
            ],
        ],
    ],
];

return [
    'datatables' => $dataTables,
    'endpoints' => $endpoints,
    'roles' => $roles,
    'languages' => $languages,
    'role_groups' => $roleGroups,
    'user_status' => $userStatus,
    'settings' => $settings,
    'settings_groups' => $settingsGroups,
    'notifications' => $notifications,
    'sidebar_routes' => $sidebarRoutes,
];
