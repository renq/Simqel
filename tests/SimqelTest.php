<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Connection;
use Simqel\Connection\PDO\MySQL;
use Simqel\Connection\PDO\PostgreSQL;
use Simqel\Connection\PDO\Sqlite;
use Simqel\Simqel;
use Simqel\Strategy\MysqlStrategy;
use Simqel\Strategy\PostgreSqlStrategy;
use Simqel\Strategy\SqliteStrategy;
use Simqel\Strategy\Strategy;

/**
 * Class SimqelTest
 * @package Simqel\Tests
 */
class SimqelTest extends TestCase
{
    public function testCreateByDSNMySQL()
    {
        $dsn = 'mysql://root:pass@localhost/db';
        $sql = Simqel::createByDSN($dsn);
        $this->assertTrue($sql instanceof Simqel, 'SQL::createByDSN should returns instance of class SQL');
        $this->assertTrue($sql->getConnection() instanceof MySQL, 'Connection should be an instance of Connection_PDO_MySQL');
        $this->assertTrue($sql->getStrategy() instanceof MysqlStrategy, 'Strategy should be an instance of Strategy_MySQL');
    }


    public function testCreateByDSNPostgresql()
    {
        if (!TEST_PGSQL) {
            $this->markTestSkipped('Tests for Postgresql are not activated.');
        }
        $dsn = 'pgsql://root:pass@localhost/db';
        $sql = Simqel::createByDSN($dsn);
        $this->assertTrue($sql instanceof Simqel, 'SQL::createByDSN should returns instance of class SQL');
        $this->assertTrue($sql->getConnection() instanceof PostgreSQL, 'Connection should be an instance of Connection_PDO_PostgreSQL');
        $this->assertTrue($sql->getStrategy() instanceof PostgreSqlStrategy, 'Strategy should be an instance of Strategy_PostgreSQL');
    }


    public function testCreateByDSNSqlite()
    {
        $dsn = 'sqlite:///some/file/';
        $sql = Simqel::createByDSN($dsn);
        $this->assertTrue($sql instanceof Simqel, 'SQL::createByDSN should returns instance of class SQL');
        $this->assertTrue($sql->getConnection() instanceof Sqlite, 'Connection should be an instance of Connection_PDO_PostgreSQL');
        $this->assertTrue($sql->getStrategy() instanceof SqliteStrategy, 'Strategy should be an instance of Strategy_Sqlite');
    }

    /**
     * @expectedException \Simqel\Exception
     */
    public function testCreateByDSNUnknownDatabase()
    {
        $dsn = 'someUnknownDatabase://someUser@someServer/someDtabase/';
        Simqel::createByDSN($dsn);
    }

    /**
     * @param $method
     */
    private function methodCall($method)
    {
        $connection = $this->getConnectionMock();
        $strategy = new MysqlStrategy($connection);
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method($method);
        $sql->$method();
    }


    public function testBeginTransaction()
    {
        $this->methodCall('beginTransaction');
    }


    public function testCommit()
    {
        $this->methodCall('commit');
    }


    public function testRollback()
    {
        $this->methodCall('rollback');
    }

    /*
    public function testGetDebug() {
        $this->methodCall('getDebug');
    }*/

    /**
     * @return Simqel
     */
    private function getSQLWithMocks()
    {
        return new Simqel($this->getConnectionMock(), $this->getStrategyMock());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSaveInvalidTableName()
    {
        $sql = $this->getSQLWithMocks();
        $sql->save(array(), array());
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSaveInvalidParams()
    {
        $sql = $this->getSQLWithMocks();
        $sql->save('table', array('ala', 'ma', 'kota'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSaveEmpryParams()
    {
        $sql = $this->getSQLWithMocks();
        $sql->save('table', array());
    }


    public function testSaveInsert()
    {
        $table = 'cat';
        $params = array('name' => 'Nennek', 'colour' => 'black');

        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method('query');
        $strategy->expects($this->once())->method('insert');
        $sql->save($table, $params);
    }


    public function testSaveUpdate()
    {
        $table = 'cat';
        $params = array('name' => 'Nennek', 'colour' => 'black');

        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method('query');
        $strategy->expects($this->once())->method('update');
        $sql->save($table, $params, 5);
    }

    /**
     * @expectedException \Simqel\Exception
     */
    public function testSaveUpdateWithWrongID()
    {
        $table = 'cat';
        $params = array('name' => 'Nennek', 'colour' => 'black');

        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method('query');
        $connection->expects($this->once())->method('getAffectedRows')->will($this->returnValue(0));
        $sql->save($table, $params, 100);
    }


    public function testDelete()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method('query');
        $strategy->expects($this->once())->method('delete');
        $sql->delete('table', 1);
    }


    public function testQuery()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->once())->method('query');
        $sql->query('SELECT * FROM table WHERE id = ?', array(1));
    }


    public function testOne()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $strategy->expects($this->once())->method('one');
        $sql->one('SELECT * FROM table WHERE id = ?', array(1));
    }


    public function testById()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $strategy->expects($this->once())->method('byId');
        $sql->byId('table', 1);
    }


    public function testValue()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->any())->method('query')->willReturn(true);
        $connection->expects($this->any())->method('fetch')
            ->willReturnOnConsecutiveCalls(['name' => 'Nennek', 'colour' => 'black'], null);
        $this->assertEquals('Nennek', $sql->value('SELECT name FROM cat WHERE id = 1'));
    }

    public function testFlat()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $connection->expects($this->at(0))->method('query')->will($this->returnValue(true));
        $connection->expects($this->exactly(3))->method('fetch')->willReturnOnConsecutiveCalls(
            array('name' => 'Nennek', 'colour' => 'black'),
            array('name' => 'Misia', 'colour' => 'striped'),
            null
        );

        $this->assertEquals(array('Nennek', 'Misia'), $sql->flat('SELECT name FROM cat'));
    }


    public function testGet()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $strategy->expects($this->never())->method('limit');
        $sql->get("SELECT * FROM cat WHERE id = ?", array(1));
    }

    public function testGetWithLimit()
    {
        $connection = $this->getConnectionMock();
        $strategy = $this->getStrategyMock();
        $sql = new Simqel($connection, $strategy);

        $strategy->expects($this->once())->method('limit')->with(
            'SELECT * FROM cat WHERE id = ?',
            10,
            20
        );
        $sql->get("SELECT * FROM cat WHERE id = ?", array(1), 10, 20);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | Connection
     */
    private function getConnectionMock()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $connection;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | Strategy
     */
    private function getStrategyMock()
    {
        $strategy = $this->getMockBuilder(Strategy::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $strategy;
    }
}
