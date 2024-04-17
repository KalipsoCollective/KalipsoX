<?php

/**
 * @package KX
 * @subpackage Model\ModuleContents
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class ModuleContents extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('module_contents');
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
                'module_id' => [
                    'type' => 'int',
                    'length' => 11,
                ],
                'data' => [
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
                    'columns' => ['module_id'],
                ],
                'full_text_search' => [
                    'type' => 'fulltext',
                    'columns' => ['data']
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
