<?php

namespace Simqel\Connection;

use Simqel\BindException;
use Simqel\Exception;
use Simqel\Settings;

/**
 *
 * Enter description here ...
 * @author Michał Lipek (michal@lipek.net)
 *
 */
abstract class PdoConnection extends BaseConnection
{
    /**
     * @var \PDO
     */
    protected $handle = null;
    /**
     * @var \PDOStatement
     */
    private $lastStatement = null;

    /**
     * PdoConnection constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    /**
     *
     */
    public function disconnect()
    {
        $this->handle = null;
    }

    /**
     * @return null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param resource $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value)
    {
        $this->connect();
        if (is_null($value)) {
            return 'NULL';
        } elseif ($value == '') {
            return "''";
        } elseif ($value{0} != '0' && (is_int($value) || is_float($value))) {
            return (string)$value;
        } elseif (is_bool($value)) {
            return (string)((int)$value);
        } elseif (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->escape($v);
            }
        } else {
            return $this->handle->quote($value);
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws BindException
     * @throws Exception
     */
    public function query($query, array $params = array())
    {
        $this->connect();
        try {
            $numberOfPlaceholders = substr_count($query, '?');
            $numberOfParams = count($params);
            if ($numberOfPlaceholders != $numberOfParams) {
                throw new BindException("Query error: wrong number of bind parameters; should be $numberOfPlaceholders; $numberOfParams given;\n$query");
            }
            list($query, $params) = $this->parseQueryArrays($query, $params);
            $sth = $this->handle->prepare($query);
            $sth->execute(array_values($params));
        } catch (\PDOException $e) {
            throw new Exception('Query error: ' . $e->getMessage() . ";\nQuery: $query\n\nParameters: " . print_r($params, true), (int)$e->getCode(), $e);
        }
        $this->lastStatement = $sth;
        return $sth;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getAffectedRows()
    {
        if ($this->lastStatement instanceof \PDOStatement) {
            return $this->lastStatement->rowCount();
        } else {
            throw new Exception("No query was executed, so method getRowsAffected is pointless in this moment.");
        }
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @return mixed
     */
    public function lastInsertId($table = '', $idColumn = '')
    {
        $this->connect();
        return $this->handle->lastInsertId();
    }

    /**
     * @param mixed $sth
     * @return mixed
     */
    public function fetch($sth)
    {
        $this->connect();
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     *
     */
    public function beginTransaction()
    {
        $this->connect();
        $this->handle->beginTransaction();
    }

    /**
     *
     */
    public function commit()
    {
        $this->connect();
        $this->handle->commit();
    }

    /**
     *
     */
    public function rollback()
    {
        $this->connect();
        $this->handle->rollback();
    }
}
