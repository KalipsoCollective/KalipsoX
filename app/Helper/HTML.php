<?php

/**
 * @package KX
 * @subpackage Helper\HTML
 */

declare(strict_types=1);

namespace KX\Helper;

use KX\Core\Helper;

class HTML
{

    public static function notificationList($notifications, $mini = true, $withParent = true)
    {
        $return = '';
        if (!empty($notifications)) {
            $return = $withParent ? '<div class="list-group list-group-flush list-group-hoverable list-group-notification">' : '';
            foreach ($notifications as $notification) {

                $notification->details = json_decode($notification->details);
                $return .= '
                <div class="list-group-item notification-' . $notification->id . '">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot d-block' . ($notification->status === 'active' ? ' status-dot-animated bg-green' : '') . '"></span></div>
                        <div class="col' . ($mini ? '  text-truncate' : '') . '">
                            <a href="javascript:;" ' . ($notification->status === 'active' ? 'data-kx-action="' . Helper::base('auth/notifications/view/' . $notification->id) . '" ' : '') . 'class="text-body d-inline-block">
                                ' . Helper::lang($notification->details->title) . '
                            </a>
                            <time class="ms-2 timeago badge badge-outline text-blue" datetime="' . date('c', (int)$notification->created_at) . '">' . date('d.m H:i', (int)$notification->created_at) . '</time>
                            <div class="d-block text-secondary mt-n1' . ($mini ? '  text-truncate' : '') . '" title="' . Helper::lang($notification->details->body) . '">
                                ' . Helper::lang($notification->details->body) . '
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:;" data-kx-again data-kx-action="' . Helper::base('auth/notifications/delete/' . $notification->id) . '" class="list-group-item-actions">
                                <i class="ti ti-trash icon"></i>
                            </a>
                        </div>
                    </div>
                </div>';
            }
            $return .= $withParent ? '</div>' : '';
        } else {
            $return = '
            <div class="card-body">
                <p class="text-center text-muted h-100 d-flex align-items-center justify-content-center">' . Helper::lang('base.no_notifications') . '</p>
            </div>';
        }
        return $return;
    }

    public static function tableLayout($type)
    {

        global $kxVariables;

        $return = '';

        if (!empty($kxVariables) && isset($kxVariables['datatables']['tables'][$type]) !== false) {
            $tableDetails = $kxVariables['datatables']['tables'][$type];
            $columns = [];

            $thead = '';
            $theadSearch = '';
            foreach ($tableDetails['columns'] as $tKey => $column) {
                $thead .= '<th>' . $column['name'] . '</th>';
                $columns[] = [
                    'name' => $tKey,
                    'visible' => $column['visible'],
                    'searchable' => $column['searchable'],
                    'orderable' => $column['orderable'],
                ];


                if ($column['searchable']) {
                    $theadSearch .= '<th class="p-1">';
                    if ($column['type'] === 'select') {
                        $theadSearch .= '<select class="form-select form-select-sm">
                            <option value="">' . Helper::lang('base.all') . '</option>';
                        foreach ($column['options'] as $oKey => $option) {
                            $theadSearch .= '<option value="' . $oKey . '">' . $option . '</option>';
                        }
                        $theadSearch .= '</select>';
                    } else {
                        $theadSearch .= '<input type="text" class="form-control form-control-sm">';
                    }
                    $theadSearch .= '</th>';
                } else {
                    $theadSearch .= '<th></th>';
                }
            }

            $return = '
            <table data-kx-table="' . $type . '" data-kx-url="' . $tableDetails['url'] . '" class="table table-striped table-bordered table-hover" data-kx-order=\'' . json_encode($tableDetails['order']) . '\' data-kx-columns=\'' . json_encode($columns) . '\'>
                <thead>
                    <tr>
                        ' . $thead . '
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        ' . $theadSearch . '
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
            ';

            $return .= self::adminModalContents($type);
        }

        return $return;
    }

    public static  function settingsCard()
    {
        global $kxVariables;

        // set up the settings
        $settings = $kxVariables['settings'];
        $settingsGroups = $kxVariables['settings_groups'];

        // group the settings
        $groupping = [];
        foreach ($settingsGroups as $groupKey => $group) {
            $group['fields'] = [];
            $groupping[$groupKey] = $group;

            foreach ($group['settings'] as $settingKey) {
                if (isset($settings[$settingKey]) !== false) {
                    $groupping[$groupKey]['fields'][$settingKey] = $settings[$settingKey];
                }
            }

            unset($groupping[$groupKey]['settings']);
        }

        // render the settings
        $return = '<div class="card">';
        $li = '';
        $content = '';
        $i = 0;
        foreach ($groupping as $groupKey => $group) {
            $i++;
            $li .= '
            <li class="nav-item" role="presentation">
                <a href="#settingsGroup' . $groupKey . '" class="nav-link' . ($i === 1 ? ' active' : '') . '" data-bs-toggle="tab" aria-selected="' . ($i === 1 ? 'true' : 'false') . '" role="tab" tabindex="-1">
                    <i class="icon me-1 ' . $group['icon'] . '"></i>
                    ' . $group['name'] . '
                </a>
            </li>';

            $content .= '
            <div id="settingsGroup' . $groupKey . '" class="card tab-pane fade' . ($i === 1 ? ' show active' : '') . '" role="tabpanel">
                <div class="card-body">
                    <div class="card-title">' . $group['name'] . '</div>
                    <div class="row">';
            global $kxAvailableLanguages, $kxLang;
            foreach ($group['fields'] as $settingKey => $setting) {

                $required = isset($setting['required']) !== false ? $setting['required'] : false;

                if (isset($setting['multilanguage']) !== false && $setting['multilanguage'] && in_array($setting['type'], ['select', 'switch']) === false) {

                    $value = json_decode($setting['value'], true);
                    $content .= '
                    <div class="' . $setting['col'] . '">
                        <div class="card mb-3 multilanguage-card">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">';
                    $cardBodyContent = '';
                    foreach ($kxAvailableLanguages as $lang) {
                        $content .= '
                        <li class="nav-item">
                            <a href="#settingsGroup' . $groupKey . $settingKey . $lang . '" class="nav-link' . ($lang === $kxLang ? ' active' : '') . '" data-bs-toggle="tab" aria-selected="' . ($lang === $kxLang ? 'true' : 'false') . '" role="tab" tabindex="-1">
                                ' . Helper::lang('langs.' . $lang) . '
                            </a>
                        </li>';
                        $cardBodyContent .= '
                        <div class="tab-pane fade' . ($lang === $kxLang ? ' show active' : '') . '" id="settingsGroup' . $groupKey . $settingKey . $lang . '" role="tabpanel">
                            <div>
                                <label class="form-label">' . $setting['label'] . ' (' . strtoupper($lang) . ')</label>
                                ' . ($setting['type'] === 'textarea' ? '
                                <textarea class="form-control" name="settings[' . $settingKey . '][' . $lang . ']"' . ($required ? ' required' : '') . '>' . (isset($value[$lang]) !== false ? $value[$lang] : '') . '</textarea>' :
                            '<input type="' . $setting['type'] . '" class="form-control" name="settings[' . $settingKey . '][' . $lang . ']" value="' . (isset($setting['value'][$lang]) !== false ? $setting['value'][$lang] : '') . '"' . ($required ? ' required' : '') . ' />') . '
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>';
                    }
                    $content .= '
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    ' . $cardBodyContent . '
                                </div>
                            </div>
                        </div>
                    </div>';
                    continue;
                }

                $content .= '
                        <div class="' . $setting['col'] . '">
                            <div class="mb-3' . ($setting['type'] === 'switch' ? ' mt-5' : '') . '">
                            ' . ($setting['type'] === 'switch' ? '' : '<label class="form-label">' . $setting['label'] . '</label>');
                if ($setting['type'] === 'select') {

                    $opts = '';
                    foreach ($setting['options'] as $oKey => $option) {
                        $opts .= '<option value="' . $oKey . '"' . ($setting['value'] === $oKey ? ' selected' : '') . '>' . $option . '</option>';
                    }
                    $content .= '
                                <select class="form-select" name="settings[' . $settingKey . ']"' . ($required ? ' required' : '') . '>
                                    ' . $opts . '
                                </select>
                                <div class="invalid-feedback"></div>';
                } elseif ($setting['type'] === 'textarea') {
                    $content .= '
                                <textarea class="form-control" name="settings[' . $settingKey . ']"' . ($required ? ' required' : '') . '>' . $setting['value'] . '</textarea>
                                <div class="invalid-feedback"></div>';
                } elseif ($setting['type'] === 'switch') {
                    $content .= '
                            <label class="form-check form-switch">
                                <input name="settings[' . $settingKey . ']" class="form-check-input" type="checkbox"' . ($setting['value'] ? ' checked' : '') . '>
                                <span class="form-check-label">' . $setting['label'] . '</span>
                            </label>';
                } else {
                    $content .= '
                                <input type="' . $setting['type'] . '" class="form-control" name="settings[' . $settingKey . ']" value="' . $setting['value'] . '"' . ($required ? ' required' : '') . ' />
                                <div class="invalid-feedback"></div>';
                }
                $content .= '
                            </div>
                        </div>';
            }
            $content .= '
                    </div>
                </div>
            </div>';
        }

        $lastUpdatedAt = (int)Helper::config('settings.last_updated_at');

        $return .= '
        <form data-kx-form action="' . Helper::base('dashboard/settings') . '" method="post" class="settings-card">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    ' . $li . '
                </ul>
                <p class="p-0 m-0 d-flex align-items-center">
                    ' . (!empty($lastUpdatedAt) ? '<span class="me-2 text-muted small fw-medium">' . Helper::lang('base.last_update') . ': <time datetime="' . date('c', $lastUpdatedAt) . '" class="timeago lite">' . date('d.m.Y H:i', $lastUpdatedAt) . '</time></span>' : '') . '
                    <i class="ti ti-assembly"></i>
                </p>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    ' . $content . '
                </div>
            </div>
            <div class="card-footer d-flex">
                <button class="btn btn-primary ms-auto" type="submit">' . Helper::lang('base.save') . '</button>
            </div>
            </div>
        </form>
        ';

        return $return;
    }

    public static function adminModalContents($type, $data = null, $onlyBody = false)
    {
        global $kxVariables;

        // set main variables
        $roles = $kxVariables['roles'];
        $roleGroups = $kxVariables['role_groups'];
        $endpoints = $kxVariables['endpoints'];
        $languages = $kxVariables['languages'];
        $userStatus = $kxVariables['user_status'];

        $return = '';

        $action = is_null($data) ? 'add' : 'edit';
        $id = is_null($data) ? '' : $data->id;

        switch ($type) {
            case 'users':
                $roleSelect = '';
                foreach ($roles as $roleId => $roleName) {
                    $selected = false;
                    if ($action === 'edit' && isset($data->role_id) && $data->role_id === $roleId) {
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
                    if ($action === 'edit' && isset($data->status) && $data->status === $status) {
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
                            <h5 class="modal-title">' . Helper::lang('base.' . $action . '_user') . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Helper::lang('base.close') . '"></button>
                        </div>
                        <form data-kx-form action="' . Helper::base($action === 'add' ? 'dashboard/users/add' : 'dashboard/users/edit/' . $id) . '" method="post" autocomplete="off">
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
                                            <label class="form-label' . ($action === 'add' ? ' required' : '') . '">' . Helper::lang('auth.password') . '</label>
                                            <input type="password" class="form-control" name="password"' . ($action === 'add' ? ' required' : '') . ' />
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
                                    <span class="btn-text"><i class="ti ti-' . ($action === 'add' ? 'plus' : 'device-floppy') . ' icon"></i>' . Helper::lang('base.' . $action) . '</span>
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
                break;
            case 'user-roles':

                $roleSelect = '';
                foreach ($roles as $roleId => $roleName) {
                    if ($action === 'edit' && isset($data->id) && (int)$data->id === (int)$roleId) {
                        continue;
                    }
                    $roleSelect .= '<option value="' .  $roleId . '">' . $roleName . '</option>';
                }

                $endpointSelect = '';
                $groupping = [];
                foreach ($endpoints as $endpoint => $endpointDetails) {
                    if (!isset($groupping[$endpointDetails['group']])) {
                        $groupping[$endpointDetails['group']] = [
                            'endpoints' => [],
                            'icon' => isset($roleGroups[$endpointDetails['group']]) !== false ? $roleGroups[$endpointDetails['group']]['icon'] : null
                        ];
                    }
                    $groupping[$endpointDetails['group']]['endpoints'][$endpoint] = $endpointDetails;
                }
                foreach ($groupping as $group => $groupEndpoints) {
                    $endpointSelect .= '
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3 d-flex align-items-center">
                                        ' . (isset($groupEndpoints['icon']) !== false ? '<i class="me-1 ' . $groupEndpoints['icon'] . '"></i>' : '') . '
                                        <span>' . Helper::lang('authorization.' . $group) . '</span>
                                        <div class="vr mx-2"></div>
                                        <button type="button" data-kx-action="switch_trigger" data-kx-selector=\'[data-selector="' . $group . '"]\' data class="btn btn-sm">' . Helper::lang('base.trigger_all') . '</button>
                                    </h5>
                                </div>';
                    foreach ($groupEndpoints['endpoints'] as $endpoint => $endpointDetails) {
                        $checked = false;
                        if ($endpointDetails['default']) {
                            $checked = true;
                        }

                        if ($action === 'edit') {
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
                                                <input data-selector="' . $group . '" name="routes[]" value="' . $endpoint . '" class="form-check-input" type="checkbox"' . ($checked ? ' checked' : '') . '>
                                                <span class="form-check-label">' . Helper::lang($endpointDetails['name']) . '</span>
                                            </label>
                                        </div>
                                    </div>';
                    }
                    $endpointSelect .= '
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
                            <h5 class="modal-title">' . Helper::lang('base.' . $action . '_user_role') . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="' . Helper::lang('base.close') . '"></button>
                        </div>
                        <form data-kx-form action="' . Helper::base($action === 'add' ? 'dashboard/user-roles/add' : 'dashboard/user-roles/edit/' . $id) . '" method="post" autocomplete="off">
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
                            ' . $endpointSelect . '
                            ' . ($action === 'edit' ? '
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
                                    <span class="btn-text"><i class="ti ti-' . ($action === 'add' ? 'plus' : 'device-floppy') . ' icon"></i>' . Helper::lang('base.' . $action) . '</span>
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

                break;
            case 'user-roles':
        }
        return $return;
    }
}
