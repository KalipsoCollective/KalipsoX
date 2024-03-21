<?php

/**
 * Datatable helper for server-side processing
 * @package KX
 * @subpackage Helper\DT
 */

declare(strict_types=1);

namespace KX\Helper;

use KX\Core\Helper;
use KX\Core\Model;

class DT
{

    protected $model;
    protected $preModel;
    protected $result;
    protected $arguments;
    protected $primaryKey;
    protected $columns;
    protected $total;
    protected $filtered;

    /**
     * DT constructor
     *
     * @param Model $model
     * 
     * @return self
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->preModel = clone $model;

        return $this;
    }

    /**
     * Extract data
     *
     * @param array $args
     * @param string $primaryKey
     * @param array $columns
     * @param string $selectQuery
     * @return array
     */
    public function extract($args, $primaryKey, $columns, $selectQuery)
    {
        $return = [
            'draw' => isset($args['draw']) ? intval($args['draw']) : 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ];

        // set properties
        $this->arguments = $args;
        $this->primaryKey = $primaryKey;
        $this->columns = $columns;

        // set table
        $this->table('(' . $selectQuery . ') t');

        // select columns
        $this->select();

        // prepare filter
        $this->filter();

        // prepare order
        $this->order();

        // prepare limit
        $this->limit();

        // prepare data
        $this->execute();

        // get data
        $return['data'] = $this->formatData();
        $return['recordsTotal'] = $this->total;
        $return['recordsFiltered'] = $this->filtered ? count($this->result) : $this->total;

        return $return;
    }

    /**
     * Set table
     *
     * @param string $selectQuery
     * @return self
     */
    public function table($selectQuery)
    {
        $this->model->table($selectQuery);
        $this->preModel->table($selectQuery);
        return $this;
    }

    /**
     * Set columns
     *
     * @return self
     */
    public function select()
    {
        $columns = [];
        foreach ($this->columns as $col) {
            $columns[] = 't.' . $col['db'];
        }
        $this->model->select(implode(', ', $columns));
        $this->preModel->select($columns[0]);
        return $this;
    }

    /**
     * Set filter
     *
     * @return self
     */
    public function filter()
    {
        if (isset($this->arguments['columns']) !== false) {
            foreach ($this->arguments['columns'] as $col) {
                if (!empty($col['search']['value'])) {
                    // like or equal ("" or [])
                    $strSplit = str_split($col['search']['value']);
                    $firstChar = $strSplit[0];
                    $lastChar = $strSplit[count($strSplit) - 1];

                    if ($firstChar === '[' && $lastChar === ']') {
                        $this->model->where('t.' . $col['name'], substr($col['search']['value'], 1, -1));
                    } else {
                        $value = '%' . trim($col['search']['value'], '][') . '%';
                        $this->model->like('t.' . $col['name'], $value);
                    }
                    $this->filtered = true;
                }
            }
        }

        return $this;
    }

    /**
     * Set order
     *
     * @return self
     */
    public function order()
    {
        if (isset($this->arguments['order']) !== false) {
            foreach ($this->arguments['order'] as $order) {
                $this->model->orderBy('t.' . $order['name'], $order['dir']);
            }
        }
        return $this;
    }

    /**
     * Set limit
     *
     * @return self
     */
    public function limit()
    {
        if (isset($this->arguments['start']) !== false && $this->arguments['length'] !== -1) {
            $this->model->pagination($this->arguments['length'], round($this->arguments['start'] / $this->arguments['length']));
        }
        return $this;
    }

    /**
     * Execute
     *
     * @return self
     */
    public function execute()
    {
        $this->result = $this->model->getAll();
        $this->total = count($this->preModel->getAll());
        return $this;
    }

    /**
     * Format data
     *
     * @return array
     */
    public function formatData()
    {
        $data = [];
        foreach ($this->result as $row) {
            $dataRow = [];
            foreach ($this->columns as $col) {
                $d = null;
                if (isset($row->{$col['db']}) !== false) {

                    $d = $row->{$col['db']};
                    if (isset($col['formatter'])) {
                        $d = $col['formatter']($d, (array)$row);
                    } else {
                        $d = $row->{$col['db']};
                    }
                }
                $dataRow[$col['dt']] = $d;
            }
            $data[] = $dataRow;
        }
        return $data;
    }
}
