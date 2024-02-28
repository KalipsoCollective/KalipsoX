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
            foreach ($tableDetails['columns'] as $tKey => $column) {
                $thead .= '<th>' . $column['name'] . '</th>';
                $columns[] = [
                    'name' => $tKey,
                    'visible' => $column['visible'],
                    'searchable' => $column['searchable'],
                    'orderable' => $column['orderable'],
                ];
            }

            $return = '
            <table data-kx-table="' . $type . '" data-kx-url="' . $tableDetails['url'] . '" class="table table-striped table-bordered table-hover" data-kx-order=\'' . json_encode($tableDetails['order']) . '\' data-kx-columns=\'' . json_encode($columns) . '\'>
                <thead>
                    <tr>
                        ' . $thead . '
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            ';
        }

        return $return;
    }
}
