<?php

/**
 * @package KX
 * @subpackage Model\Modules
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Modules extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('modules');
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
                    'nullable' => true,
                ],
                'structure' => [
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
                'full_text_search' => [
                    'type' => 'fulltext',
                    'columns' => ['structure']
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
