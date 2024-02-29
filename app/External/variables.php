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

$userStatus = [
    'active' => Helper::lang('base.active'),
    'passive' => Helper::lang('base.passive'),
    'deleted' => Helper::lang('base.deleted'),
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

                            if (Helper::authorization('dashboard/users/delete/:id')) {
                                $return .= '
                                    <a data-kx-again data-kx-action="' . Helper::base('dashboard/users/delete/' . $row['id']) . '" href="javascript:;" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="' . Helper::lang('base.delete') . '">
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
                        $selected = false;
                        if ($type === 'edit' && isset($data->status) && $data->status === $status) {
                            $selected = true;
                        }
                        $statusSelect .= '<option value="' . $status . '"' . ($selected ? ' selected' : '') . '>' . $name . '</option>';
                    }

                    $return = '';
                    if (!$onlyBody) {
                        $return = '
                        <div class="modal modal-blur fade" id="addUserModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="addUserModalContent">';
                    }

                    $return .= '
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">' . Helper::lang('base.add_user') . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Helper::lang('base.close') . '"></button>
                        </div>
                        <form data-kx-form action="' . Helper::base('dashboard/users/add') . '" method="post" autocomplete="off">
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
                                            <label class="form-label required">' . Helper::lang('auth.password') . '</label>
                                            <input type="password" class="form-control" name="password" required />
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
                                <span class="btn-text"><i class="ti ti-plus icon"></i>' . Helper::lang('base.add') . '</span>
                            </button>
                        </div>
                    </div>';

                    if (!$onlyBody) {
                        $return .= '
                            </div>
                        </div>';
                    }
                    return $return;
                }
            ]
        ],
        'default' => []
    ],
];
