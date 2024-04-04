<?php

/**
 * @package KX
 * @subpackage Model\Menus
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Menus extends Model
{

    function __construct()
    {

        // set table name
        $this->setTable('menus');
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
                'menu_key' => [
                    'type' => 'varchar',
                    'length' => 250,
                    'nullable' => true,
                ],
                'menu_items' => [
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
                    'columns' => ['menu_key'],
                ],
                'full_text_search' => [
                    'type' => 'fulltext',
                    'columns' => ['menu_items']
                ],
            ],
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
        ]);

        parent::__construct();
    }
}
