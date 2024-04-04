<?php

/**
 * @package KX
 * @subpackage Model\Notifications
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Notifications extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('notifications');

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
                'user_id' => [
                    'type' => 'int',
                    'length' => 11,
                    'nullable' => false,
                    'default' => '0',
                ],
                'type' => [
                    'type' => 'varchar',
                    'length' => 250,
                    'nullable' => false,
                ],
                'details' => [
                    'type' => 'text',
                ],
                'created_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                ],
                'viewed_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => true,
                ],
                'deleted_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => true,
                ],
                'status' => [
                    'type' => 'enum(\'active\',\'viewed\',\'deleted\')',
                    'nullable' => false,
                    'default' => 'active'
                ]
            ],
            'indexes' => [
                'primary_key' => [
                    'type' => 'primary',
                    'columns' => ['id'],
                ],
                'indexed_fields' => [
                    'type' => 'index',
                    'columns' => ['user_id', 'type', 'status'],
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
