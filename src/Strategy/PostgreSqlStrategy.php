<?php

namespace Simqel\Strategy;

use Simqel\Connection\Connection;

/**
 * Class PostgreSqlStrategy
 * @package Simqel\Strategy
 */
class PostgreSqlStrategy extends Strategy
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
        return "$query LIMIT $limit OFFSET $offset";
    }
}
