<?php

/**
 * @package KX
 * @subpackage Model\Files
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Files extends Model
{

  function __construct()
  {

    // set table name
    $this->setTable('files');
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
          'length' => 250,
          'nullable' => true,
        ],
        'module' => [
          'type' => 'varchar',
          'length' => 250,
          'nullable' => true,
        ],
        'size' => [
          'type' => 'bigint',
          'length' => 20,
        ],
        'mime' => [
          'type' => 'varchar',
          'length' => 250,
          'nullable' => true,
        ],
        'files' => [
          'type' => 'text',
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
          'columns' => ['name', 'module', 'size', 'mime']
        ],
        'full_text_search' => [
          'type' => 'fulltext',
          'columns' => ['files']
        ],
      ],
      'engine' => 'InnoDB',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_520_ci',
    ]);

    parent::__construct();
  }
}
