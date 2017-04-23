<?php

namespace Simqel\Strategy;

use Simqel\Connection\Connection;

/**
 * Class PostgreSqlStrategy
 * @package Simqel\Strategy
 */
class PostgreSqlStrategy extends Strategy
{
    /**
     * PostgreSqlStrategy constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->escapeIdentifierCharacter = '"';
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public function limit($query, $limit, $offset)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        return "$query LIMIT $limit OFFSET $offset";
    }
}
