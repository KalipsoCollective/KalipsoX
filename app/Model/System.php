<?php

/**
 * @package KX
 * @subpackage Model\System
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class System extends Model
{

	function __construct()
	{

		$this->setTable('sessions');
		$this->setSchema([
			'columns' => [
				'id' => [
					'type' => 'int',
					'length' => 11,
					'null' => false,
					'auto_increment' => true,
					'primary' => true,
				],
				'keyy' => [
					'type' => 'varchar',
					'length' => 255,
					'null' => false,
				],
				'valuee' => [
					'type' => 'text',
					'null' => false,
				],
				'created_at' => [
					'type' => 'datetime',
					'null' => false,
				],
				'json' => [
					'type' => 'json',
					'null' => false,
				],
				'text' => [
					'type' => 'text',
					'null' => false,
				],
				'updated_at' => [
					'type' => 'datetime',
					'null' => false,
				],
			],
			'indexes' => [
				'keyy' => [
					'type' => 'primary',
					'columns' => ['id'],
				],
				'created_at' => [
					'type' => 'unique',
					'columns' => ['created_at'],
				],
				'updated_at' => [
					'type' => 'index',
					'columns' => ['valuee(255)', 'updated_at'],
				],
				'text' => [
					'type' => 'fulltext',
					'columns' => ['text'],
				],
			],
			'engine' => 'InnoDB',
			'charset' => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_520_ci',
		]);

		parent::__construct();
	}
}
