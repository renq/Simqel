<?php

namespace Simqel\Connection;

use Simqel\Settings;

/**
 * Class ConnectionDecorator
 * @package Simqel\Connection
 */
class ConnectionDecorator implements Connection
{
    /**
     * @var
     */
    private $decorated;

    /**
     * ConnectionDecorator constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->decorated = $connection;
    }

    /**
     * @return Connection
     */
    public function getDecorated()
    {
        return $this->decorated;
    }

    /**
     * @return mixed
     */
    public function connect()
    {
        return $this->decorated->connect();
    }

    /**
     * @return mixed
     */
    public function disconnect()
    {
        return $this->decorated->disconnect();
    }

    /**
     * @param mixed $queryResult
     * @return array
     */
    public function fetch($queryResult)
    {
        return $this->decorated->fetch($queryResult);
    }

    /**
     * @return mixed
     */
    public function beginTransaction()
    {
        return $this->decorated->beginTransaction();
    }

    /**
     * @return mixed
     */
    public function commit()
    {
        return $this->decorated->commit();
    }

    /**
     * @return mixed
     */
    public function rollback()
    {
        return $this->decorated->rollback();
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @return int
     */
    public function lastInsertId($table = '', $idColumn = '')
    {
        return $this->decorated->lastInsertId($table, $idColumn);
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->decorated->getHandle();
    }

    /**
     * @param resource $handle
     */
    public function setHandle($handle)
    {
        $this->decorated->setHandle($handle);
    }

    /**
     * @param $variable
     * @return mixed
     */
    public function escape($variable)
    {
        return $this->decorated->escape($variable);
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function query($query, array $params = array())
    {
        return $this->decorated->query($query, $params);
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->decorated->getAffectedRows();
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->decorated->getSettings();
    }
}
