<?php

namespace Simqel\Tests;

use Simqel\Settings;
use Simqel\Connection_PDO_Sqlite;


class SqlConnectionPDOSqliteTest extends \PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		$db  = SQLITE_DB;
		$settings = new Settings("sqlite:///$db");
		$this->connection = new Connection_PDO_Sqlite($settings);
	}
	
	
    public function testConnect() {
    	$handle = $this->connection->getHandle();
    	$this->assertNull($handle, "Connection should be null before connect."); 
    	$this->connection->connect();
    	$handle = $this->connection->getHandle();
    	$this->assertTrue($handle instanceof \PDO, "After connect handle should be instance of PDO, but it is: ".get_class($handle));
    }
    
    
    public function testConnectFail() {
    	$this->setExpectedException('\Simqel\Exception');
    	$db  = 'file/not/found/db.sqlite';
		$settings = new Settings("sqlite:///$db");
		$connection = new Connection_PDO_Sqlite($settings);
    	$connection->connect();
    }
    
    
	public function testConnectionLazyLoad() {
    	$this->connection->query("SELECT DATE('now')");
    	$handle = $this->connection->getHandle();
    	$this->assertTrue($handle instanceof \PDO, "After connect handle should be instance of PDO, but it is: ".get_class($handle));
    }


}
