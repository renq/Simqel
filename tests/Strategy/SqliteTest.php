<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Connection;
use Simqel\Strategy\SqliteStrategy;

/**
 * Class SqlStrategyMySQLTest
 * @package Simqel\Tests
 */
class SqlStrategyMySQLTest extends TestCase
{
    public function testLimit()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $strategy = new SqliteStrategy($connection);
        $query = "SELECT * FROM dual";
        $queryWithLimit = $strategy->limit($query, 20, 100);
        $queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);

        $this->assertEquals("$query LIMIT 100, 20", $queryWithLimit);
    }
}
