<?php

/**
 * We use this file to specify authorization points and language definitions for display.
 * ex: [endpoint] => ['default' => (checked status), 'name' => (language definition for display)]
 */

return [
    // Dashboard
    'dashboard' => [
        'default' => true,
        'name' => 'authorization.dashboard',
    ],
    'dashboard.data.:table' => [
        'default' => true,
        'name' => 'authorization.table_data',
    ],
    'dashboard.settings' => [
        'default' => true,
        'name' => 'authorization.settings',
    ],
    // Users
    'dashboard.users' => [
        'default' => true,
        'name' => 'authorization.users',
    ],
    'dashboard.users.add' => [
        'default' => true,
        'name' => 'authorization.users_add',
    ],
    'dashboard.users.edit.:id' => [
        'default' => true,
        'name' => 'authorization.users_edit',
    ],
    'dashboard.users.delete.:id' => [
        'default' => true,
        'name' => 'authorization.users_delete',
    ],
    // User Roles
    'dashboard.user_roles' => [
        'default' => true,
        'name' => 'authorization.user_roles',
    ],
    'dashboard.user_roles.add' => [
        'default' => true,
        'name' => 'authorization.user_roles_add',
    ],
    'dashboard.user_roles.edit.:id' => [
        'default' => true,
        'name' => 'authorization.user_roles_edit',
    ],
    'dashboard.user_roles.delete.:id' => [
        'default' => true,
        'name' => 'authorization.user_roles_delete',
    ],
];
