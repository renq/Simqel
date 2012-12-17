<?php

namespace Simqel\Tests;

use Simqel\Strategy_MySQL;


class SqlStrategyMySQLTest extends \PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('Simqel\Settings');
    	$connection = $this->getMock('Simqel\Connection', array(), array($settings));
    	$strategy = new Strategy_MySQL($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 100, 20", $queryWithLimit);
    }


}

