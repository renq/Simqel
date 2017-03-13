<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Settings;
use Simqel\Connection_PDO_PostgreSQL;

/**
 * Class SqlConnectionPDOPostgreSQLTest
 * @package Simqel\Tests
 */
class SqlConnectionPDOPostgreSQLTest extends TestCase
{

    /**
     * @var
     */
    protected $connection;


    protected function setUp()
    {
        if (!TEST_PGSQL) {
            $this->markTestSkipped('Tests for Postgresql are not activated.');
        }
        $dsn = PGSQL_DSN;
        $settings = new Settings($dsn);
        $this->connection = new Connection_PDO_PostgreSQL($settings);
    }


    public function testConnect()
    {
        $this->connection->connect();
        $this->assertTrue($this->connection->getHandle() instanceof \PDO);
    }


    public function testConnectFail()
    {
        $this->setExpectedException('Simqel\Exception');
        $settings = new Settings('pgsql://super_mega_user:pass@localhost/fake_db');
        $connection = new Connection_PDO_PostgreSQL($settings);
        $connection->connect();
    }


    public function testPasswordHide()
    {
        $password = $this->connection->getSettings()->getPassword();
        $this->connection->connect();
        $hiddenPassword = $this->connection->getSettings()->getPassword();
        $this->assertFalse($password == $hiddenPassword, 'After connect password should be removed from the settings object for security reasons.');
    }


    public function testSerialSequence()
    {
        $this->connection->connect();
        $this->connection->query("DROP TABLE IF EXISTS cats");
        $this->connection->query("
            CREATE TABLE cats (
                id serial NOT NULL PRIMARY KEY,
                \"name\" character varying(50),
                  colour character varying(50)
            )");
        $this->assertStringEndsWith('cats_id_seq', $this->connection->getSerialSequence('cats', 'id'));
    }


    public function testLastInsertId()
    {
        $this->connection->connect();
        $this->connection->query("DROP TABLE cats");
        $this->connection->query("
            CREATE TABLE cats (
                id serial NOT NULL PRIMARY KEY,
                \"name\" character varying(50),
                  colour character varying(50)
            )");
        $this->connection->query("INSERT INTO cats (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
        $this->assertEquals(1, $this->connection->lastInsertId('cats', 'id'));
    }

    public function testSerialSequenceCache()
    {
        $settings = $this->getMock('Simqel\Settings');
        $connection = $this->getMock('Simqel\Connection_PDO_PostgreSQL', array('query', 'connect', 'fetch'), array($settings));
        $connection->expects($this->once())->method('query');
        $connection->expects($this->once())->method('fetch')->will($this->returnValue('cats_id_seq'));
        $connection->getSerialSequence('cats', 'id');
        $connection->getSerialSequence('cats', 'id');
    }
}
