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
					'nullable' => false,
					'auto_increment' => true,
					'primary' => true,
				],
				'keyy' => [
					'type' => 'varchar',
					'length' => 255,
					'nullable' => false,
				],
				'valuee' => [
					'type' => 'text',
					'nullable' => false,
				],
				'created_at' => [
					'type' => 'datetime',
					'nullable' => false,
				],
				'json' => [
					'type' => 'json',
					'nullable' => false,
				],
				'text' => [
					'type' => 'text',
					'nullable' => false,
				],
				'sync' => [
					'type' => 'varchar',
					'length' => 255,
					'nullable' => true,
					'default' => null,
				],
				'updated_at' => [
					'type' => 'datetime',
					'nullable' => false,
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

		$this->setBulkData([
			[
				'keyy' => 'test',
				'valuee' => 'test',
				'created_at' => '2020-01-01 00:00:00',
				'updated_at' => '2020-01-01 00:00:00',
				'text' => 'test',
				'json' => '{"test": "test"}',
			],
			[
				'keyy' => 'test2',
				'valuee' => 'test2',
				'created_at' => '2022-01-01 00:00:00',
				'updated_at' => '2022-01-01 00:00:00',
				'text' => 'test2',
				'json' => '{"test": "test2"}',
			],
		]);

		parent::__construct();
	}
}
