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
}
