<?php

/**
 * @package KX
 * @subpackage Model\Logs
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Logs extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('logs');
        // set table attributes
        $this->created();

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
                'endpoint' => [
                    'type' => 'text',
                    'nullable' => false,
                ],
                'status_code' => [
                    'type' => 'int'
                ],
                'method' => [
                    'type' => 'varchar',
                    'length' => 10,
                    'nullable' => false,
                    'default' => 'GET'
                ],
                'auth_token' => [
                    'type' => 'varchar',
                    'length' => 650,
                    'nullable' => true,
                ],
                'ip' => [
                    'type' => 'varchar',
                    'length' => 180,
                    'nullable' => true,
                ],
                'header' => [
                    'type' => 'varchar',
                    'length' => 180,
                    'nullable' => true,
                ],
                'exec_time' => [
                    'type' => 'float(8,4)',
                    'default' => 0
                ],
            ],
            'indexes' => [
                'primary_key' => [
                    'type' => 'primary',
                    'columns' => ['id'],
                ],
                'indexed_fields' => [
                    'type' => 'index',
                    'columns' => ['ip', 'status_code', 'method', 'exec_time', 'created_at']
                ],
                'fulltext_fields' => [
                    'type' => 'fulltext',
                    'columns' => ['auth_token', 'header', 'endpoint'],
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
