<?php

/**
 * @package KX
 * @subpackage Model\Emails
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Emails extends Model
{

  function __construct()
  {

    // set table name
    $this->setTable('emails');

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
        'email' => [
          'type' => 'varchar',
          'length' => 250,
          'nullable' => true,
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
        'updated_at' => [
          'type' => 'varchar',
          'length' => 150,
          'nullable' => true,
        ],
        'status' => [
          'type' => 'enum(\'pending\',\'uncompleted\',\'completed\',\'cancelled\')',
          'nullable' => false,
          'default' => 'pending'
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
