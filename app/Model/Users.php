<?php

/**
 * @package KX
 * @subpackage Model\Users
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Users extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('users');
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
                'u_name' => [
                    'type' => 'varchar',
                    'length' => 100,
                    'nullable' => false,
                ],
                'f_name' => [
                    'type' => 'varchar',
                    'length' => 100,
                    'nullable' => true,
                ],
                'l_name' => [
                    'type' => 'varchar',
                    'length' => 100,
                    'nullable' => true,
                ],
                'email' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => false,
                ],
                'password' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => false,
                ],
                'token' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => false,
                ],
                'role_id' => [
                    'type' => 'int',
                    'length' => 11,
                    'nullable' => false,
                    'default' => '0',
                ],
                'b_date' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => true,
                ],
                'status' => [
                    'type' => 'enum(\'active\',\'passive\',\'deleted\')',
                    'nullable' => false,
                    'default' => 'passive'
                ]
            ],
            'indexes' => [
                'primary_key' => [
                    'type' => 'primary',
                    'columns' => ['id'],
                ],
                'unique_fields' => [
                    'type' => 'unique',
                    'columns' => ['token', 'u_name', 'email'],
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        // set sample data

        $this->setBulkData([
            [
                'u_name' => 'root',
                'email' => 'hello@koalapix.com',
                'token' => Helper::tokenGenerator(),
                'password' => password_hash('1234', PASSWORD_DEFAULT),
                'f_name' => 'Kalipso',
                'l_name' => 'Collective',
                'role_id' => 1,
                'status' => 'active'
            ],
        ]);


        parent::__construct();
    }
}
