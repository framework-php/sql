<?php

namespace GuilhermeHideki\Database\SQL\Schema;

class Table
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $database;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * Table constructor.
     *
     * @param string $id
     * @param string $name
     * @param array  $fields
     * @param string $alias
     * @param string $database
     */
    public function __construct($id, $name, $fields, $alias='', $database='')
    {
        $this
            ->setId($id)
            ->setName($name)
            ->setFields($fields)
            ->setAlias($alias)
            ->setDatabase($database)
        ;
    }

    /**
     * @param string $name
     * @param bool   $alias
     *
     * @return mixed
     */
    public function getField($name, $alias=false)
    {
        return ($alias ? $this->getAlias() . '.' : '') . $this->fields[$name];
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the table name
     *
     * @param bool $withAlias
     *
     * @return string
     */
    public function getName($withAlias = false)
    {
        return $withAlias ? $this->getAliasedName() : $this->name;
    }

    /**
     * @return string
     */
    public function getAliasedName()
    {
        return $this->name . ' as ' . $this->getAlias();
    }


    /**
     * @param bool   $alias
     *
     * @return string
     */
    public function getFullTableName($alias=false)
    {
        return sprintf('%s.%s',
            $this->getDatabase(),
            $this->getName($alias));
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     *
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = trim($database);

        return $this;
    }
}