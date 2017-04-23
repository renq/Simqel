<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Connection;
use Simqel\Strategy\PostgreSqlStrategy;
use Simqel\Strategy_PostgreSQL;

/**
 * Class SqlStrategyPostgreSQLTest
 * @package Simqel\Tests
 */
class SqlStrategyPostgreSQLTest extends TestCase
{
    public function testLimit()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $strategy = new PostgreSqlStrategy($connection);
        $query = "SELECT * FROM dual";
        $queryWithLimit = $strategy->limit($query, 20, 100);
        $queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);

        $this->assertEquals("$query LIMIT 20 OFFSET 100", $queryWithLimit);
    }
}
