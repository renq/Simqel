<?php

namespace Simqel\Connection\PDO;

use Simqel\Connection\PdoConnection;
use Simqel\Exception;
use Simqel\Settings;

/**
 * Class Connection_PDO_Sqlite
 * @package Simqel
 */
class Sqlite extends PdoConnection
{
    /**
     * Sqlite constructor.
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
            $database = $this->settings->getDatabase();

            $this->handle = new \PDO("{$driver}:{$database}");
            $this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (\PDOException $e) {
            throw new Exception('Can\'t open database: ' . $e->getMessage());
        }
    }
}
