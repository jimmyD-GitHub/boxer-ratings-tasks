<?php

namespace App\Service;

use Exception;
use App\Entity\Division;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ConnectionException;

class RatingsUpdater
{
    /** @var Connection */
    private $connection;

    /** @var int */
    private $multiplier = 3;

    /** @var int */
    private $maxRated = 10;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Division $division
     * @return bool
     * @throws ServiceException
     */
    public function update(Division $division): bool
    {
        $rating = 1;
        $updated = false;
        $boxerIds = [];
        $tableName = $division->getTableName();
        $divisionId = $division->getId();

        try {

            $ratings = $this->calculateRatings($tableName);

            if (empty($ratings)) {
                return $updated;
            }

            $this->connection->beginTransaction();

            foreach ($ratings as $boxer) {
                $boxer['rating'] = $rating;
                $boxerIds[] = (int)$boxer['boxerId'];
                $updated = $this->updateRatings($boxer, $divisionId);
                $rating++;
            }

            $this->cleanupRatings($boxerIds, $divisionId);
            $this->connection->commit();

        } catch (Exception $exception) {

            try {
                $this->connection->rollBack();
            } catch (ConnectionException $exception) {
                throw new ServiceException($exception->getMessage());
            }

            throw new ServiceException($exception->getMessage());
        }

        return $updated;
    }

    /**
     * @param string $tableName
     * @return array
     * @throws DBALException
     */
    private function calculateRatings(string $tableName): array
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('boxer_id AS boxerId', "SUM((11 - rating) * $this->multiplier) AS points")
            ->from($tableName)
            ->groupBy('boxer_id')
            ->addOrderBy('points', 'DESC')
            ->setMaxResults($this->maxRated);

        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $boxer
     * @param int $divisionId
     * @return bool
     * @throws DBALException
     */
    private function updateRatings(array $boxer, int $divisionId): bool
    {
        $update = 'INSERT INTO `rating` (`division_id`, `boxer_id`, `rating`, `points`) ' .
            'VALUES (:divisionId, :boxerId, :rating, :points) ' .
            'ON DUPLICATE KEY UPDATE `rating` = :rating, `points` = :points';

        $stmt = $this->connection->prepare($update);

        $stmt->bindValue('divisionId', $divisionId, ParameterType::INTEGER);
        $stmt->bindValue('boxerId', (int)$boxer['boxerId'], ParameterType::INTEGER);
        $stmt->bindValue('rating', (int)$boxer['rating'], ParameterType::INTEGER);
        $stmt->bindValue('points', (int)$boxer['points'], ParameterType::INTEGER);

        return $stmt->execute();
    }

    /**
     * @param array $boxerIds
     * @param int $divisionId
     * @return bool
     * @throws DBALException
     */
    private function cleanupRatings(array $boxerIds, int $divisionId): bool
    {
        $delete = 'DELETE FROM `rating` WHERE `boxer_id` NOT IN (?) AND `division_id` = ?';

        return (bool)$this->connection->executeUpdate($delete,
            array($boxerIds, $divisionId),
            array(Connection::PARAM_INT_ARRAY, ParameterType::INTEGER)
        );
    }
}