<?php

namespace Simqel\Connection\PDO;

use Simqel\Connection\PdoConnection;
use Simqel\Exception;
use Simqel\Settings;

/**
 * Class Connection_PDO_PostgreSQL
 * @package Simqel
 */
class PostgreSQL extends PdoConnection
{
    /**
     * @var array
     */
    private $serialSequenceCache = array();

    /**
     * PostgreSQL constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function connect()
    {
        if ($this->handle instanceof \PDO) {
            return false;
        }
        try {
            $driver = $this->settings->getDriver();
            $host = $this->settings->getHost();
            $port = ($port = $this->settings->getPort()) ? $port : 5432;
            $username = $this->settings->getUsername();
            $password = $this->settings->getPassword();
            $database = $this->settings->getDatabase();

            $this->handle = new \PDO("{$driver}:host={$host};port=$port;dbname={$database}", $username, $password);
            $this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->settings->clearPassword();
            return true;
        } catch (\PDOException $e) {
            throw new Exception('Can\'t open database: ' . $e->getMessage());
        }
    }

    /**
     * @param $table
     * @param $idColumn
     * @return mixed
     */
    public function getSerialSequence($table, $idColumn)
    {
        if (isset($this->serialSequenceCache[$table][$idColumn])) {
            return $this->serialSequenceCache[$table][$idColumn];
        }
        $sth = $this->query("SELECT pg_get_serial_sequence(?, ?) as seq_name", array($table, $idColumn));
        $row = $this->fetch($sth);
        $this->serialSequenceCache[$table][$idColumn] = $row['seq_name'];
        return $row['seq_name'];
    }

    /**
     * @param string $table
     * @param string $idColumn
     * @return mixed
     */
    public function lastInsertId($table = '', $idColumn = '')
    {
        $this->connect();
        $sequenceName = $this->getSerialSequence($table, $idColumn);
        return $this->handle->lastInsertId($sequenceName);
    }
}
