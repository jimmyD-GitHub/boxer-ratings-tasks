<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection;

class RatingsUpdater
{
    /** @var Connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $divisionId
     * @return bool
     */
    public function update(int $divisionId): bool
    {
        return true;
    }
}