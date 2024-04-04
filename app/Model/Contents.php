<?php

/**
 * @package KX
 * @subpackage Model\Contents
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Contents extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('contents');
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
                'module' => [
                    'type' => 'varchar',
                    'length' => 100,
                    'nullable' => true,
                ],
                'input' => [
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
                    'columns' => ['module'],
                ],
                'full_text_search' => [
                    'type' => 'fulltext',
                    'columns' => ['input']
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
