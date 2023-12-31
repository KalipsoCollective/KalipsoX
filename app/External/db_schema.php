<?php

/**
 * Database Structure
 *
 * The schema here is used to add new tables or to add new columns to existing tables.
 * Column parameters is as follows.
 *
 * > type:          Type parameters(required) -> (INT | VARCHAR | TEXT | DATE | ENUM | JSON)
 * > nullable:      True if it is an empty field.
 * > auto_inc:      True if it is an auto increment field.
 * > attr:          Attribute parameters -> (BINARY | UNSIGNED | UNSIGNED ZEROFILL | ON UPDATE CURRENT_TIMESTAMP)
 * > type_values:   ENUM -> ['on', 'off'] | INT, VARCHAR -> 255
 * > default:       Default value -> NULL, 'string' or CURRENT_TIMESTAMP
 * > index:         Index type -> (INDEX | PRIMARY | UNIQUE | FULLTEXT)
 */

return [
  'tables' => [

    /* Users Table */
    'users' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'UNSIGNED',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'u_name' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'index'         => 'UNIQUE'
        ],
        'f_name' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'default'       => 'NULL',
          'nullable'      => 'true',
          'index'         => 'INDEX'
        ],
        'l_name' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'default'       => 'NULL',
          'nullable'      => 'true',
          'index'         => 'INDEX'
        ],
        'email' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'UNIQUE'
        ],
        'password' => [
          'type'          => 'varchar',
          'type_values'   => 120,
        ],
        'token' => [
          'type'          => 'varchar',
          'type_values'   => 80,
        ],
        'role_id' => [
          'type'          => 'int',
          'type_values'   => 2,
          'default'       => 'NULL',
          'nullable'      => 'true',
          'index'         => 'INDEX'
        ],
        'details' => [
          'type'          => 'json',
          'nullable'      => 'true',
          'default'       => 'NULL',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'default'       => 0,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'status' => [
          'type'          => 'enum',
          'type_values'   => ['active', 'passive', 'deleted'],
          'default'       => 'active',
          'index'         => 'INDEX'
        ]
      ],
    ],

    /* User Roles Table */
    'user_roles' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'name' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'UNIQUE',
        ],
        'routes' => [
          'type'          => 'text',
          'nullable'      => true
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
      ],
    ],

    /* Sessions Table */
    'sessions' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'auth_code' => [
          'type'          => 'varchar',
          'type_values'   => 50,
          'index'         => 'UNIQUE',
        ],
        'user_id' => [
          'type'          => 'int',
          'index'         => 'INDEX',
        ],
        'header' => [
          'type'          => 'varchar',
          'type_values'   => 250,
        ],
        'ip' => [
          'type'          => 'varchar',
          'type_values'   => 250,
        ],
        'role_id' => [
          'type'          => 'varchar',
          'type_values'   => '80',
          'index'         => 'INDEX',
        ],
        'update_data' => [
          'type'          => 'json',
          'nullable'      => true,
        ],
        'last_action_date' => [
          'type'          => 'varchar',
          'type_values'   => 80,
        ],
        'last_action_point' => [
          'type'          => 'varchar',
          'nullable'      => true,
          'type_values'   => 250,
        ]
      ]
    ],

    /* Email Logs Table */
    'email_logs' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'date' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX',
        ],
        'email' => [
          'type'          => 'varchar',
          'type_values'   => 180,
          'index'         => 'INDEX',
        ],
        'name' => [
          'type'          => 'varchar',
          'type_values'   => 250,
          'index'         => 'INDEX',
        ],
        'title' => [
          'type'          => 'varchar',
          'type_values'   => 120,
          'index'         => 'INDEX',
        ],
        'user_id' => [
          'type'          => 'int',
          'index'         => 'INDEX',
        ],
        'sender_id' => [
          'type'          => 'int',
          'index'         => 'INDEX',
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'file' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX',
        ],
        'status' => [
          'type'          => 'enum',
          'type_values'   => ['pending', 'uncompleted', 'completed'],
          'default'       => 'pending',
          'index'         => 'INDEX'
        ],
      ]
    ],

    /* Notifications Table */
    'notifications' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'user_id' => [
          'type'          => 'int',
          'index'         => 'INDEX',
        ],
        'type' => [
          'type'          => 'varchar',
          'type_values'   => 140,
          'index'         => 'INDEX',
        ],
        'external_datas' => [
          'type'          => 'text',
          'index'         => 'FULLTEXT',
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'viewed_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX',
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'deleted_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX',
          'nullable'      => true,
          'default'       => 'NULL',
        ],
      ]
    ],

    /* Logs Table */
    'logs' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'endpoint' => [
          'type'          => 'text',
          'index'         => 'FULLTEXT'
        ],
        'method' => [
          'type'          => 'varchar',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL',
          'index'         => 'INDEX'
        ],
        'controller' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'nullable'      => true,
          'default'       => 'NULL',
          'index'         => 'INDEX'
        ],
        'middleware' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'nullable'      => true,
          'default'       => 'NULL',
          'index'         => 'INDEX'
        ],
        'http_status' => [
          'type'          => 'int',
          'index'         => 'INDEX'
        ],
        'auth_code' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL',
          'index'         => 'INDEX'
        ],
        'ip' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL',
          'index'         => 'INDEX'
        ],
        'header' => [
          'type'          => 'varchar',
          'type_values'   => 180,
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'request' => [
          'type'          => 'text',
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'response' => [
          'type'          => 'text',
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'exec_time' => [
          'type'          => 'float',
          'type_values'   => '5,4',
          'default'       => 0,
          'index'         => 'INDEX'
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'default'       => 0,
          'index'         => 'INDEX'
        ]
      ]
    ],

    /* Contents Table */
    'contents' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'module' => [
          'type'          => 'varchar',
          'type_values'   => 80,
        ],
        'input' => [
          'type'          => 'json',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
      ],
    ],

    /* Forms Table */
    'forms' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'form' => [
          'type'          => 'varchar',
          'type_values'   => 80,
        ],
        'input' => [
          'type'          => 'json',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'status' => [
          'type'          => 'enum',
          'type_values'   => ['pending', 'in_action', 'completed', 'deleted'],
          'default'       => 'pending',
          'index'         => 'INDEX'
        ]
      ],
    ],

    /* Files Table */
    'files' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'unsigned',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'module' => [
          'type'          => 'varchar',
          'type_values'   => 230,
          'index'     => 'INDEX'
        ],
        'mime' => [
          'type'          => 'varchar',
          'type_values'   => 230,
          'index'     => 'INDEX'
        ],
        'size' => [
          'type'          => 'bigint',
          'index'     => 'INDEX',
          'type_values'   => 20,
          'attr'          => 'UNSIGNED',
        ],
        'name' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'index'     => 'INDEX'
        ],
        'files' => [
          'type'          => 'json',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL'
        ],
      ],
    ],

    /* Menus Table */
    'menus' => [
      'cols' => [
        'id' => [
          'type'          => 'int',
          'auto_inc'      => true,
          'attr'          => 'UNSIGNED',
          'type_values'   => 11,
          'index'         => 'PRIMARY'
        ],
        'menu_key' => [
          'type'          => 'varchar',
          'type_values'   => 255,
          'index'         => 'UNIQUE'
        ],
        'items' => [
          'type'          => 'json',
        ],
        'created_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'index'         => 'INDEX'
        ],
        'created_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'default'       => 0,
          'index'         => 'INDEX'
        ],
        'updated_at' => [
          'type'          => 'varchar',
          'type_values'   => 80,
          'nullable'      => true,
          'default'       => 'NULL',
        ],
        'updated_by' => [
          'type'          => 'int',
          'type_values'   => 10,
          'nullable'      => true,
          'default'       => 'NULL',
        ]
      ],
    ],

  ],
  'table_values' => [
    'charset'   => 'utf8mb4', // You can use 'utf8' if the structure is causing problems.
    'collate'   => 'utf8mb4_unicode_520_ci', // You can use 'utf8_general_ci' if the structure is causing problems.
    'engine'    => 'InnoDB',
    'specific'  => [ // You can give specific value.
      'sessions' => [
        // 'engine'    => 'MEMORY'
      ],
    ]
  ],
  'data'  => [
    'users' => [
      [
        'u_name'                => 'root',
        'f_name'                => NULL,
        'l_name'                => NULL,
        'email'                 => 'hello@koalapix.com',
        'password'              => '$2y$10$1i5w0tYbExemlpAAsospSOZ.n06NELYooYa5UJhdytvBEn85U8lly', // 1234
        'token'                 => 'Hl7kojH2fLdsbMUO8T0lZdTcMwCjvOGIbBk8cndJSsh2IcpN',
        'role_id'               => '1',
        'created_at'            => time(),
        'created_by'            => 0,
        'status'                => 'active'
      ],
    ],
    'user_roles' => [
      [
        'name'                  => 'admin',
        'routes'                => 'auth,auth/:action,auth/logout,management,management/users,management/users/list,management/users/add,management/users/:id,management/users/:id/update,management/users/:id/delete,management/roles,management/roles/list,management/roles/add,management/roles/:id,management/roles/:id/delete,management/roles/:id/update,management/sessions,management/sessions/list,management/sessions/:id/delete,management/icon-picker,management/:module,management/:module/list,management/:module/add,management/:module/:id,management/:module/:id/update,management/:module/:id/delete,management/:module/slug,management/:module/autocomplete,management/content/:module/upload-file,management/media,management/media/list,management/media/add,management/media/:id,management/media/:id/update,management/media/:id/delete,management/menus,management/menus/list,management/menus/add,management/menus/:id,management/menus/:id/update,management/menus/:id/delete,management/menus/get-menu-params,management/forms/:form,management/forms/:form/list,management/forms/:form/:id,management/forms/:form/:id/update,management/forms/:form/:id/delete,management/logs,management/logs/list,management/logs/:ip/block,management/settings,management/settings/update',
        'created_at'            => time(),
        'created_by'            => 1
      ]
    ],
    'menus' => [
      [
        'menu_key'              => 'top',
        'items'                => '[{"name":{"en":"Home","tr":"Anasayfa"},"blank":false,"direct_link":"","dynamic_link":{"module":"basic_home","parameter":""}},{"name":{"en":"Login","tr":"Giriş"},"blank":false,"direct_link":"","dynamic_link":{"module":"basic_login","parameter":""}},{"name":{"en":"Services","tr":"Hizmetler"},"blank":false,"direct_link":"","dynamic_link":{"module":"modules_services","parameter":"list_as_dropdown"}},{"name":{"en":"Contact","tr":"İletişim"},"blank":false,"direct_link":"","dynamic_link":{"module":"forms_contact-form","parameter":"detail"}},{"name":{"en":"New Page","tr":"Yeni Sayfa"},"blank":true,"direct_link":"","dynamic_link":{"module":"forms_contact-form","parameter":"detail"}}]',
        'created_at'            => time(),
        'created_by'            => 1
      ]
    ],
  ],
];
