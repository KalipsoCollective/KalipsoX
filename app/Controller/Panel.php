<?php

/**
 * @package KX
 * @subpackage Controller\Panel
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;
use KX\Core\Model;


use KX\Core\Request;
use KX\Core\Response;

use KX\Controller\Notification;

use KX\Helper\SSP;

final class Panel
{

    public function dashboard(Request $request, Response $response)
    {
        return $response->render('panel/dashboard', [
            'title' => Helper::lang('base.dashboard'),
            'description' => Helper::lang('base.dashboard_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function settings(Request $request, Response $response)
    {
        return $response->render('panel/settings', [
            'title' => Helper::lang('base.settings'),
            'description' => Helper::lang('base.settings_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function users(Request $request, Response $response)
    {
        return $response->render('panel/users', [
            'title' => Helper::lang('base.users'),
            'description' => Helper::lang('base.users_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function userRoles(Request $request, Response $response)
    {
        return $response->render('panel/user_roles', [
            'title' => Helper::lang('base.user_roles'),
            'description' => Helper::lang('base.user_roles_desc'),
            'auth' => $request->getMiddlewareParams(),
        ], 'layout');
    }

    public function tableData(Request $request, Response $response)
    {

        global $kxVariables;

        extract(Helper::input([
            'table' => 'nulled_text',
        ], $request->getRouteDetails()->attributes));

        if (empty($table)) {
            return $response->json(['error' => 'Table name is required']);
        } else if (!isset($kxVariables['datatables']['tables'][$table])) {
            return $response->json(['error' => 'Table not found']);
        } else {
            $tableDetails = $kxVariables['datatables']['tables'][$table];

            $columns = [];
            $i = 0;
            foreach ($tableDetails['columns'] as $tKey => $column) {
                if ($tKey === 'actions') {
                    continue;
                }
                $columns[] = [
                    'db' => $tKey,
                    'dt' => $i,
                ];
                $i++;
            }

            $sqlSelect = '(
                SELECT *, role_id as role FROM users
            ) as result';

            $model = new Model();
            $ssp = SSP::simple_extend($_POST, $model->pdo, 'id', $columns, $sqlSelect);

            return $response->json($ssp);
        }
    }
}
