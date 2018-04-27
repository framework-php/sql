<?php

namespace GuilhermeHideki\Database\SQL;

/**
 * SQL Class
 *
 * @package GuilhermeHideki\Database\SQL
 */
class Query
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $joins = [];

    /**
     * @var array
     */
    public $where = [];

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    public $groupBy = [];

    /**
     * @var array
     */
    public $orderBy = [];

    /**
     * @var array
     */
    public $updates = [];

    /**
     * @var array
     */
    public $inserts = [];

    public function __construct($table='')
    {
        $this->setFrom($table);
    }

    /**
     * Static constructor
     *
     * @param string $table Table's name
     *
     * @return static
     */
    public static function From($table)
    {
        return new static($table);
    }

    public function asSubquery($alias)
    {
        return "({$this->getSelect()}) as $alias";
    }

    /**
     * Sets the "from" table
     *
     * @param string $table
     *
     * @return static
     */
    public function setFrom($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Returns the SQL query
     *
     * @return string SQL Query
     */
    public function getSelect()
    {
        $selectFields = empty($this->fields) ? '*': implode(', ', $this->fields);

        return implode(' ', [
            "SELECT $selectFields FROM {$this->table}",
            implode(' ', $this->joins),
            $this->getWhereClause(),
            empty($this->groupBy) ? '' : 'GROUP BY '.implode(', ', $this->groupBy),
            empty($this->orderBy) ? '' : 'ORDER BY '.implode(', ', $this->orderBy)
        ]);
    }

    /**
     * Returns the SQL query
     *
     * @return string SQL Query
     */
    public function getInsert()
    {
        $table = implode('', [
            $this->table !== null ? $this->table : '',
            !empty($this->fields) ? sprintf('(%s)', implode(', ', $this->fields)) : ''
        ]);

        $insertValues = array_map('array_values', $this->inserts);
        $bindings = array_merge(...$insertValues);

        $placeholders = $this->getPlaceholdersSequential($insertValues);
        if (trim($placeholders) === '') {
            $sql = '';
        } else {
            $sql = "INSERT INTO $table VALUES $placeholders";
        }

        return [
            'sql' => $sql,
            'bindings' => $bindings
        ];
    }

    /**
     * Returns the SQL query
     *
     * @return string SQL Query
     */
    public function getUpdate()
    {
        if (empty($this->updates)) {
            return '';
        }

        $updateFields = implode(', ', $this->updates);

        return implode(' ', [
            "UPDATE {$this->table} SET $updateFields",
            $this->getWhereClause()
        ]);
    }

    /**
     * Returns the SQL query
     *
     * @return string SQL Query
     */
    public function getDelete()
    {
        return implode(' ', [
            "DELETE FROM {$this->table}",
            $this->getWhereClause()
        ]);
    }

    protected function getPlaceholdersSequential($data)
    {
        return implode(', ', array_map(function ($item) {
            if (count($item)) {
                return sprintf('(%s)', implode(', ', array_fill(0, count($item), '?')));
            }
        }, $data));
    }

    /**
     * Adds the SQL binding
     *
     * @param string $placeholder PDO placeholder format ":placeholder"
     * @param mixed  $value       O valor do item
     *
     * @return $this
     */
    public function addBinding($placeholder, $value)
    {
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addBindings($fields)
    {
        return $this->add('bindings', $fields);
    }


    /**
     * @param array|string $fields
     *
     * @return $this
     */
    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }

        foreach ((array)$fields as $field) {
            if (is_string($field)) {
                $this->addField($field);
            } else {
                $this->addField($field[0], $field[1]);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addField($field, $alias = '')
    {
        if ($alias === '') {
            $this->fields[] = $field;
        } else {
            $this->fields[] = "$field AS $alias";
        }

        return $this;
    }

    /**
     * @param array $queries
     * @param array $bindings
     *
     * @return $this
     */
    public function addUpdates($queries, $bindings=[])
    {
        $this->add('updates', $queries);

        if (!empty($bindings)) {
            $this->addBindings($bindings);
        }

        return $this;
    }

    /**
     * Adiciona um join no SQL
     *
     * @param string|array $data
     *
     * @return $this
     */
    public function addJoins($data)
    {
        return $this->add('joins', $data);
    }

    /**
     * Adiciona um where no SQL
     *
     * @param string|array $data
     * @param array        $bindings
     *
     * @return $this
     */
    public function addWhere($data, array $bindings=[])
    {
        $this->add('where', $data);

        if (!empty($bindings)) {
            $this->addBindings($bindings);
        }

        return $this;
    }

    /**
     * @param string $id  Identificador
     * @param string $key Nome do campo
     *
     * @return $this
     */
    public function withId($id, $key='Id')
    {
        if ($id !== null) {
            $this->addWhere("$key = :Id", [
                ':Id' => $id
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addOrderBy($data, $sort='')
    {
        return $this->add('orderBy', $data.' '.$sort);
    }

    /**
     * @return $this
     */
    public function addGroupBy($field)
    {
        return $this->add('groupBy', $field);
    }

    /**
     * Adiciona os items no campo desejado
     *
     * @param string       $field O nome do campo
     * @param string|array $data  Os dados
     *
     * @return $this
     */
    private function add($field, $data)
    {
        if (is_string($data)) {
            $data = [$data];
        }

        $this->$field = array_merge($this->$field, $data);

        return $this;
    }

    /**
     * @return string
     */
    private function getWhereClause()
    {
        return empty($this->where) ? '' : 'WHERE ' . implode(' AND ', $this->where);
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @param array $bindings
     * @return Query
     */
    public function setBindings($bindings)
    {
        $this->bindings = $bindings;

        return $this;
    }
}
