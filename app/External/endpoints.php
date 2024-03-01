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
        'default' => false,
        'name' => 'authorization.table_data',
    ],
    'dashboard.settings' => [
        'default' => false,
        'name' => 'authorization.settings',
    ],
    // Users
    'dashboard.users' => [
        'default' => false,
        'name' => 'authorization.users',
    ],
    'dashboard.users.add' => [
        'default' => false,
        'name' => 'authorization.users_add',
    ],
    'dashboard.users.edit.:id' => [
        'default' => false,
        'name' => 'authorization.users_edit',
    ],
    'dashboard.users.delete.:id' => [
        'default' => false,
        'name' => 'authorization.users_delete',
    ],
    // User Roles
    'dashboard.user_roles' => [
        'default' => false,
        'name' => 'authorization.user_roles',
    ],
    'dashboard.user_roles.add' => [
        'default' => false,
        'name' => 'authorization.user_roles_add',
    ],
    'dashboard.user_roles.edit.:id' => [
        'default' => false,
        'name' => 'authorization.user_roles_edit',
    ],
    'dashboard.user_roles.delete.:id' => [
        'default' => false,
        'name' => 'authorization.user_roles_delete',
    ],
];
