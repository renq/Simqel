<?php

namespace Simqel\Tests;

use PHPUnit\Framework\TestCase;
use Simqel\Connection\Connection;
use Simqel\Strategy\MysqlStrategy;
use Simqel\Strategy_MySQL;

/**
 * Class StrategyTest
 * @package Simqel\Tests
 */
class StrategyTest extends TestCase
{


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | Connection
     */
    private $connection;

    /**
     * @var Strategy_MySQL
     */
    private $strategy;


    public function setUp()
    {
        $this->connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategy = new MysqlStrategy($this->connection);
    }


    public function testOne()
    {
        $this->assertEquals("query", $this->strategy->one("query"));
    }


    private function removeDoubleSpaces($string)
    {
        return trim(preg_replace('/[\s]+/', ' ', $string));
    }


    public function testById()
    {
        $e = $this->strategy->getEscapeIdentifierCharacter();
        $byId = $this->removeDoubleSpaces($this->strategy->byId('table', 'table_id'));
        $this->assertEquals("SELECT * FROM {$e}table{$e} WHERE {$e}table_id{$e} = ?", $byId);
    }


    public function testInsert()
    {
        $e = $this->strategy->getEscapeIdentifierCharacter();
        $params = array(
            'name' => 'My Name',
            'city' => 'Warsaw',
            'when' => '2010-02-20',
        );
        $query = $this->removeDoubleSpaces($this->strategy->insert('table', $params));
        $expected = "INSERT INTO {$e}table{$e} ({$e}name{$e}, {$e}city{$e}, {$e}when{$e}) VALUES (?, ?, ?)";
        $this->assertEquals($expected, $query);
    }


    public function testUpdate()
    {
        $e = $this->strategy->getEscapeIdentifierCharacter();
        $params = array(
            'name' => 'My Name',
            'city' => 'Warsaw',
            'when' => '2010-02-20',
        );
        $query = $this->removeDoubleSpaces($this->strategy->update('table', $params, 'id'));
        $expected = "UPDATE {$e}table{$e} SET {$e}name{$e} = ?, {$e}city{$e} = ?, {$e}when{$e} = ? WHERE {$e}id{$e} = ?";
        $this->assertEquals($expected, $query);
    }


    public function testDelete()
    {
        $e = $this->strategy->getEscapeIdentifierCharacter();
        $query = $this->removeDoubleSpaces($this->strategy->delete('table', 'id'));
        $expected = "DELETE FROM {$e}table{$e} WHERE {$e}id{$e} = ?";
        $this->assertEquals($expected, $query);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getStrategyMock()
    {
        return $this->getMockBuilder(Strategy_MySQL::class)
            ->setConstructorArgs([$this->connection])
            ->setMethods(['escapeIdentifier'])
            ->getMock();
    }


    public function testByIdUsingEscapeIdentifier()
    {
        $this->strategy->setEscapeIdentifierCharacter('|');
        $this->assertEquals(
            "SELECT * FROM |table| WHERE |id| = ?",
            trim($this->strategy->byId('table', 'id'))
        );
    }

    public function testDescribe()
    {
        $query = $this->removeDoubleSpaces($this->strategy->describe());
        $expected = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = ? AND table_name = ?";
        $this->assertEquals($expected, $query);
    }


    public function testEscapeIdentifierSetterAndGetter()
    {
        $e = '\\';
        $this->strategy->setEscapeIdentifierCharacter($e);
        $this->assertEquals($e, $this->strategy->getEscapeIdentifierCharacter());
    }


    public function testEscapeIdentifier()
    {
        $this->strategy->setEscapeIdentifierCharacter('"');
        $this->assertEquals('"table"', $this->strategy->escapeIdentifier('table'));
        $this->assertEquals('"database"."table"', $this->strategy->escapeIdentifier('database.table'));
        $this->assertEquals('"database"."table"."field"', $this->strategy->escapeIdentifier('database.table.field'));
        $this->strategy->setEscapeIdentifierCharacter('%^%');
        $this->assertEquals('%^%database%^%.%^%table%^%.%^%field%^%', $this->strategy->escapeIdentifier('database.table.field'));
    }
}
