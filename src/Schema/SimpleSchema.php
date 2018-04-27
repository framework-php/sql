<?php

namespace GuilhermeHideki\Database\SQL\Schema;

/**
 * Manage simple collections
 *
 * @package GuilhermeHideki\Database\SQL\Schema
 */
class SimpleSchema
{
    /**
     * @var Table[]
     */
    protected $tables = [];

    /**
     * @param string $id
     *
     * @return Table
     */
    public function getTable($id)
    {
        return $this->tables[$id];
    }

    /**
     * @param Table $table
     */
    public function setTable($table)
    {
        $this->tables[$table->getId()] = $table;
    }

    /**
     * @param Table[] $tables
     *
     * @return $this
     */
    public function setTables($tables)
    {
        foreach ($tables as $table) {
            $this->setTable($table);
        }

        return $this;
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}