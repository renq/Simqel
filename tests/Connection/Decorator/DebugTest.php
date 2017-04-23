<?php

namespace Simqel\Tests\Connection\Decorator;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Decorator\DebugDecorator;
use Simqel\Connection\PDO\Sqlite;

/**
 * Class SqlConnectionDecoratorDebugTest
 * @package Simqel\Tests
 */
class DebugTest extends TestCase
{

    /**
     * All queries should be logged inside this decorator. By using getDebug() method user can get all
     * SQL queries which were run.
     */
    public function testLogDebugMessages()
    {
        $connection = $this->getConnection();
        $decorator = new DebugDecorator($connection);
        $connection->expects($this->any())->method('fetch')->will($this->returnValue(15));

        $this->assertEquals([], $decorator->getDebug());
        $decorator->query("SOME QUERY");
        $this->assertEquals(["SOME QUERY"], $decorator->getDebug());
        $decorator->query("NEXT QUERY ?", [15]);
        $this->assertEquals(["SOME QUERY", "NEXT QUERY 15"], $decorator->getDebug());

        $decorator->beginTransaction();
        $debug = $decorator->getDebug();
        $this->assertEquals('BEGIN TRANSACTION', end($debug));

        $decorator->commit();
        $debug = $decorator->getDebug();
        $this->assertEquals('COMMIT', end($debug));

        $decorator->rollback();
        $debug = $decorator->getDebug();
        $this->assertEquals('ROLLBACK', end($debug));
    }

    /**
     * At the beginning array with debug information should be empty.
     * After some query, it should has one element with executed query.
     */
    public function testArray()
    {
        $connection = $this->getConnection();
        $decorator = new DebugDecorator($connection);

        $this->assertEquals([], $decorator->getDebug());
        $decorator->query("SELECT * FROM tab WHERE id IN ?", [[1, 2, 3]]);
        $this->assertEquals(["SELECT * FROM tab WHERE id IN (1, 2, 3)"], $decorator->getDebug());
    }

    /**
     * Get connection mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject | Sqlite
     */
    private function getConnection()
    {
        $connection = $this->getMockBuilder(Sqlite::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $connection;
    }
}
