<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\PDO\MySQL;
use Simqel\Settings;

/**
 * Class SqlConnectionPDOMySQLTest
 * @package Simqel\Tests
 */
class SqlConnectionPDOMySQLTest extends TestCase
{
    /**
     * @var MySQL
     */
    protected $connection;

    /**
     * Set up.
     */
    protected function setUp()
    {
        $dsn = MYSQL_DSN;
        $settings = new Settings($dsn);
        $this->connection = new MySQL($settings);
    }

    /**
     * Test connect to database. Method should return a PDO instance.
     */
    public function testConnect()
    {
        $this->connection->connect();
        $this->assertTrue($this->connection->getHandle() instanceof \PDO);
    }

    /**
     * @expectedException \Simqel\Exception
     */
    public function testConnectFail()
    {
        $settings = new Settings('mysql://super_user@localhost/fake_db');
        $connection = new MySQL($settings);
        $connection->connect();
    }

    public function testPasswordHide()
    {
        $password = $this->connection->getSettings()->getPassword();
        $this->connection->connect();
        $hiddenPassword = $this->connection->getSettings()->getPassword();
        $this->assertFalse($password == $hiddenPassword, 'After connect password should be removed from the settings object for security reasons.');
    }

    public function testAffectedRows()
    {
        $db = MYSQL_DB;
        $settings = new Settings($db);
        $database = $settings->getDatabase();
        $settings->setDatabase(null);
        $this->connection = new MySQL($settings);
        $this->connection->query("DROP DATABASE IF EXISTS $database;");
        $this->connection->query("CREATE DATABASE $database;");
        $this->connection->query("USE $database;");
        $settings->setDatabase($database);
        $this->connection->query("CREATE TABLE cat (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50),
            colour VARCHAR(50)
        ) ENGINE=InnoDB");

        $this->connection->query("INSERT INTO cat VALUES(1, 'Theta', 'red')");
        $this->assertEquals(1, $this->connection->getAffectedRows());
        $this->connection->query("UPDATE cat SET colour = ? WHERE id = ?", array('pink', 1));
        $this->assertEquals(1, $this->connection->getAffectedRows());
        $this->connection->query("UPDATE cat SET colour = ? WHERE id = ?", array('pink', 1));
        $this->assertEquals(1, $this->connection->getAffectedRows());
    }
}
