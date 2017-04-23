<?php

namespace Simqel\Connection\Decorator;

use Simqel\Connection\Connection;
use Simqel\Connection\ConnectionDecorator;

/**
 * Class Connection_Decorator_Debug
 * @package Simqel
 */
class DebugDecorator extends ConnectionDecorator
{
    /**
     * @var Connection
     */
    private $decorated;

    /**
     * @var array
     */
    private $queries = array();

    /**
     * DebugDecorator constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->decorated = $connection;
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function query($query, array $params = array())
    {
        $this->queries[] = $this->buildQuery($query, $params);
        return $this->decorated->query($query, $params);
    }

    /**
     * @return mixed
     */
    public function beginTransaction()
    {
        $this->queries[] = 'BEGIN TRANSACTION';
        return $this->decorated->beginTransaction();
    }

    /**
     * @return mixed
     */
    public function commit()
    {
        $this->queries[] = 'COMMIT';
        return $this->decorated->commit();
    }

    /**
     * @return mixed
     */
    public function rollback()
    {
        $this->queries[] = 'ROLLBACK';
        return $this->decorated->rollback();
    }

    /**
     * @return array
     */
    public function getDebug()
    {
        return $this->queries;
    }

    /**
     * @param $variable
     * @return string
     */
    public function escape($variable)
    {
        if (is_array($variable)) {
            return '(' . implode(', ', array_map(array($this, 'escape'), $variable)) . ')';
        } elseif (is_int($variable)) {
            return (string)$variable;
        } else {
            return "'{$variable}'";
        }
    }

    /**
     * @param $query
     * @param $params
     * @return string
     */
    private function buildQuery($query, $params)
    {
        $count = 0;
        $query = str_replace(array('%', '?'), array('%%', "%s"), $query, $count);
        foreach ($params as &$param) {
            $param = $this->escape($param);
        }
        return vsprintf($query, $params);
    }
}
