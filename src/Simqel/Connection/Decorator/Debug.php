<?php

namespace Simqel;

class Connection_Decorator_Debug extends Connection_Decorator
{

    private $decorated;
    private $queries = array();

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->getDecorated() = $connection;
    }

    public function query($query, array $params = array())
    {
        $this->queries[] = $this->buildQuery($query, $params);
        return $this->getDecorated()->query($query, $params);
    }

    public function beginTransaction()
    {
        $this->queries[] = 'BEGIN TRANSACTION';
        return $this->getDecorated()->beginTransaction();
    }

    public function commit()
    {
        $this->queries[] = 'COMMIT';
        return $this->getDecorated()->commit();
    }

    public function rollback()
    {
        $this->queries[] = 'ROLLBACK';
        return $this->getDecorated()->rollback();
    }

    public function getDebug()
    {
        return $this->queries;
    }

    private function buildQuery($query, $params)
    {
        $count = 0;
        $query = str_replace(array('%', '?'), array('%%', "'%s'"), $query, $count);
        foreach ($params as $param) {
            $param = $this->escape($param);
        }
        return vsprintf($query, $params);
    }
}
