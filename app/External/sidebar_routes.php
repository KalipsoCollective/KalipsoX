<?php

return [
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
        ],
    ],
    'settings' => [
        'icon' => 'ti ti-settings',
        'link' => '/dashboard/settings',
    ],
];
