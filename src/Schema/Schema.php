<?php

namespace GuilhermeHideki\Database\SQL\Schema;

/**
 * Schema with "parameter bag"
 *
 * @package GuilhermeHideki\Database\SQL\Schema
 */
class Schema extends SimpleSchema
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @return array[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array[] $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $item
     *
     * @return mixed
     */
    public function get($item)
    {
        return $this->data[$item];
    }

    /**
     * @return string[]
     */
    public function getTableNames()
    {
        return array_keys($this->tables);
    }

    /**
     * @param string $database
     * @param bool   $override
     */
    public function setDatabaseName($database, $override = false)
    {
        foreach ($this->tables as $table) {
            if ($override || $table->getDatabase() === '') {
                $table->setDatabase($database);
            }
        }
    }
}