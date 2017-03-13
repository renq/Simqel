<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Connection;
use Simqel\Strategy\MysqlStrategy;

/**
 * Class SqlStrategySqliteTest
 * @package Simqel\Tests
 */
class SqlStrategySqliteTest extends TestCase
{

    /**
     *
     */
    public function testLimit()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $strategy = new MysqlStrategy($connection);
        $query = "SELECT * FROM dual";
        $queryWithLimit = $strategy->limit($query, 20, 100);
        $queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);

        $this->assertEquals("$query LIMIT 100, 20", $queryWithLimit);
    }
}

