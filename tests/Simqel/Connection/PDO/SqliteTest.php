<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\PDO\Sqlite;
use Simqel\Settings;

/**
 * Class SqlConnectionPDOSqliteTest
 * @package Simqel\Tests
 */
class SqlConnectionPDOSqliteTest extends TestCase
{

    /**
     * @var Connection_PDO_Sqlite
     */
    protected $connection;

    /**
     * Set up.
     */
    protected function setUp()
    {
        $db = SQLITE_DB;
        $settings = new Settings("sqlite:///$db");
        $this->connection = new Sqlite($settings);
    }


    public function testConnect()
    {
        $handle = $this->connection->getHandle();
        $this->assertNull($handle, "Connection should be null before connect.");
        $this->connection->connect();
        $handle = $this->connection->getHandle();
        $this->assertTrue($handle instanceof \PDO, "After connect handle should be instance of PDO, but it is: " . get_class($handle));
    }

    /**
     * @expectedException \Simqel\Exception
     */
    public function testConnectFail()
    {
        $db = 'file/not/found/db.sqlite';
        $settings = new Settings("sqlite:///$db");
        $connection = new Sqlite($settings);
        $connection->connect();
    }


    public function testConnectionLazyLoad()
    {
        $this->connection->query("SELECT DATE('now')");
        $handle = $this->connection->getHandle();
        $this->assertTrue($handle instanceof \PDO, "After connect handle should be instance of PDO, but it is: " . get_class($handle));
    }
}
