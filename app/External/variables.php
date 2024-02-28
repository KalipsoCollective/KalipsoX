<?php

use KX\Core\Helper;

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
                    ],
                    'u_name' => [
                        'name' => Helper::lang('auth.username'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'f_name' => [
                        'name' => Helper::lang('auth.first_name'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,

                    ],
                    'l_name' => [
                        'name' => Helper::lang('auth.last_name'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'email' => [
                        'name' => Helper::lang('auth.email'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,

                    ],
                    'role' => [
                        'name' => Helper::lang('auth.role'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'b_date' => [
                        'name' => Helper::lang('auth.birthdate'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'status' => [
                        'name' => Helper::lang('base.status'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'created_at' => [
                        'name' => Helper::lang('base.created_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'updated_at' => [
                        'name' => Helper::lang('base.updated_at'),
                        'visible' => true,
                        'searchable' => true,
                        'orderable' => true,
                    ],
                    'actions' => [
                        'name' => Helper::lang('base.actions'),
                        'visible' => true,
                        'searchable' => false,
                        'orderable' => false,
                    ],
                ],
                'order' => [
                    'name' => 'id',
                    'dir' => 'asc',
                ],
                'url' => Helper::base('dashboard/data/users'),
            ]
        ],
        'default' => []
    ],
];
