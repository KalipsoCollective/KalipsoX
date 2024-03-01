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

            if (isset($tableDetails['modal']) !== false) {
                $return .= $tableDetails['modal']();
            }
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
            foreach ($group['fields'] as $settingKey => $setting) {

                $required = isset($setting['required']) !== false ? $setting['required'] : false;
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
                                <select class="form-select" name="' . $settingKey . '"' . ($required ? ' required' : '') . '>
                                    ' . $opts . '
                                </select>
                                <div class="invalid-feedback"></div>';
                } elseif ($setting['type'] === 'textarea') {
                    $content .= '
                                <textarea class="form-control" name="' . $settingKey . '"' . ($required ? ' required' : '') . '>' . $setting['value'] . '</textarea>
                                <div class="invalid-feedback"></div>';
                } elseif ($setting['type'] === 'switch') {
                    $content .= '
                            <label class="form-check form-switch">
                                <input name="' . $settingKey . '" class="form-check-input" type="checkbox"' . ($setting['value'] ? ' checked' : '') . '>
                                <span class="form-check-label">' . $setting['label'] . '</span>
                            </label>';
                } else {
                    $content .= '
                                <input type="' . $setting['type'] . '" class="form-control" name="' . $settingKey . '" value="' . $setting['value'] . '"' . ($required ? ' required' : '') . ' />
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

        $return .= '
        <form action="' . Helper::base('settings/save') . '" method="post">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    ' . $li . '
                </ul>
                <p class="p-0 m-0"><i class="ti ti-assembly"></i></p>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    ' . $content . '
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">' . Helper::lang('base.save') . '</button>
            </div>
        </form>
        ';

        return $return;
    }
}
