<?php

return [
  'dashboard' => [
    'icon' => 'ti ti-layout-dashboard',
    'link' => '/dashboard',
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
