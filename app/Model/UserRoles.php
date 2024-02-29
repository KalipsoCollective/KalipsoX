<?php

/**
 * @package KX
 * @subpackage Model\UserRoles
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class UserRoles extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('user_roles');
        // set table attributes
        $this->created();
        $this->updated();

        // set schema
        $this->setSchema([
            'columns' => [
                'id' => [
                    'type' => 'int',
                    'length' => 11,
                    'nullable' => false,
                    'auto_increment' => true,
                    'primary' => true,
                ],
                'name' => [
                    'type' => 'varchar',
                    'length' => 100,
                    'nullable' => false,
                ],
                'routes' => [
                    'type' => 'text',
                    'nullable' => true,
                ],
            ],
            'indexes' => [
                'primary_key' => [
                    'type' => 'primary',
                    'columns' => ['id'],
                ],
                'unique_fields' => [
                    'type' => 'unique',
                    'columns' => ['name'],
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        // set sample data
        $this->setBulkData([
            [
                'name' => 'admin',
                'routes' => 'dashboard,dashboard.settings,dashboard.users,dashboard.user_roles,dashboard.data.:table,dashboard.users.add,dashboard.users.edit.:id,dashboard.users.delete.:id,dashboard.user_roles.add,dashboard.user_roles.edit.:id,dashboard.user_roles.delete.:id',
            ],
        ]);


        parent::__construct();
    }
}
