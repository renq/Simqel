<?php

namespace Simqel\Strategy;

use Simqel\Connection\Connection;

/**
 * Class SqliteStrategy
 * @package Simqel
 */
class SqliteStrategy extends Strategy
{


    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->escapeIdentifierCharacter = '"';
    }


    public function limit($query, $limit, $offset)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        return "$query LIMIT $offset, $limit";
    }


}