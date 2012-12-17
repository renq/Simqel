<?php

namespace Simqel\Tests;

use Simqel\Strategy_PostgreSQL;


class SqlStrategyPostgreSQLTest extends \PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('Simqel\Settings');
    	$connection = $this->getMock('Simqel\Connection', array(), array($settings));
    	$strategy = new Strategy_PostgreSQL($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 20 OFFSET 100", $queryWithLimit);
    }


}

