<?php

namespace Simqel\Tests;

use Simqel\Connection_Decorator_Debug;
use Simqel\Settings;

class SqlConnectionDecoratorDebugTest extends \PHPUnit_Framework_TestCase {

	public function testDebug() {
		$settings = new Settings();
		$connection = $this->getMock('Simqel\Connection_PDO_Sqlite', array(), array($settings));
		$decorator = new Connection_Decorator_Debug($connection);
		$connection->expects($this->any())->method('fetch')->will($this->returnValue(15));

		$this->assertEquals(array(), $decorator->getDebug());
		$decorator->query("SOME QUERY");
		$this->assertEquals(array("SOME QUERY"), $decorator->getDebug());
		$decorator->query("NEXT QUERY ?", array(15));
		$this->assertEquals(array("SOME QUERY", "NEXT QUERY 15"), $decorator->getDebug());

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

	public function testArray() {
		$settings = new Settings();
		$connection = $this->getMock('Simqel\Connection_PDO_Sqlite', array(), array($settings));
		$decorator = new Connection_Decorator_Debug($connection);

		$this->assertEquals(array(), $decorator->getDebug());
		$decorator->query("SELECT * FROM tab WHERE id IN ?", array(array(1, 2, 3)));
		$this->assertEquals(array("SELECT * FROM tab WHERE id IN (1, 2, 3)"), $decorator->getDebug());
	}

}

