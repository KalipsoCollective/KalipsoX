<?php

/**
 * @package KX
 * @subpackage Model\Logs
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Sessions extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('sessions');

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
                    'type' => 'int'
                ],
                'auth_token' => [
                    'type' => 'varchar',
                    'length' => 650,
                    'nullable' => true,
                ],
                'ip' => [
                    'type' => 'varchar',
                    'length' => 180,
                ],
                'header' => [
                    'type' => 'varchar',
                    'length' => 180,
                ],
                'last_act_on' => [
                    'type' => 'varchar',
                    'length' => 150,
                ],
                'last_act_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                ],
                'created_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                ],
                'expire_at' => [
                    'type' => 'varchar',
                    'length' => 150,
                    'nullable' => true,
                ],
            ],
            'indexes' => [
                'primary_key' => [
                    'type' => 'primary',
                    'columns' => ['id'],
                ],
                'indexed_fields' => [
                    'type' => 'index',
                    'columns' => ['user_id', 'last_act_on', 'last_act_at', 'created_at', 'expire_at'],
                ],
                'indexed_external' => [
                    'type' => 'index',
                    'columns' => ['ip', 'header'],
                ],
                'fulltext_fields' => [
                    'type' => 'fulltext',
                    'columns' => ['auth_token'],
                ],
                'unique_fields' => [
                    'type' => 'unique',
                    'columns' => ['auth_token'],
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
