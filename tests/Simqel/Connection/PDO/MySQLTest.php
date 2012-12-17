<?php

namespace Simqel\Tests;

use Simqel\Connection_PDO_MySQL;
use Simqel\Settings;


class SqlConnectionPDOMySQLTest extends \PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		$dsn = MYSQL_DSN;
		$settings = new Settings($dsn);
		$this->connection = new Connection_PDO_MySQL($settings);
	}
	
	
	public function testConnect() {
		$this->connection->connect();
		$this->assertTrue($this->connection->getHandle() instanceof \PDO);
	}
	
	
	public function testConnectFail() {
		$this->setExpectedException('Simqel\Exception');
		$settings = new Settings('mysql://super_mega_user@localhost/fake_db');
		$connection = new Connection_PDO_MySQL($settings);
		$connection->connect();
	}
	
	
    public function testPasswordHide() {
    	$password = $this->connection->getSettings()->getPassword();
    	$this->connection->connect();
    	$hiddenPassword = $this->connection->getSettings()->getPassword();
    	$this->assertFalse($password == $hiddenPassword, 'After connect password should be removed from the settings object for security reasons.');
    }


}
