<?php

namespace App\Service;

use DateTime;
use Exception;
use DateTimeZone;
use App\Entity\Division;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Driver\Exception as DbException;

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
     * @throws ServiceException|DbException
     */
    public function update(Division $division): bool
    {
        $rating = 1;
        $updated = false;
        $boxerIds = [];
        $divisionId = $division->getId();

        try {

            $ratings = $this->calculateRatings($division);

            if (empty($ratings)) {
                return $this->dropRatings($divisionId);
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
     * @param Division $division
     * @return array
     * @throws DbException
     * @throws DBALException
     * @throws Exception
     */
    private function calculateRatings(Division $division): array
    {
        $tableName = $division->getTableName();
        $query = $this->connection->createQueryBuilder();

        $query->select('r.boxer_id AS boxerId', "SUM((11 - r.rating) * $this->multiplier) AS points")
            ->from($tableName, 'r')
            ->join('r', 'boxer', 'b', 'r.boxer_id = b.id')
            ->join('r', 'rating_update', 'ru',
                'r.user_id = ru.user_id AND ru.division_id = :divisionId')
            ->where('r.rating <= :maxRated')
            ->andWhere('b.enabled = 1')
            ->andWhere('b.division_id = :divisionId')
            ->andWhere('ru.updated_at > :lastUpdated')
            ->groupBy('r.boxer_id')
            ->addOrderBy('points', 'DESC')
            ->setMaxResults($this->maxRated);

        $stmt = $this->connection->prepare($query);

        $stmt->bindValue('maxRated', $this->maxRated, ParameterType::INTEGER);
        $stmt->bindValue('divisionId', $division->getId(), ParameterType::INTEGER);

        $lastUpdated = new DateTime('-2 month', new DateTimeZone('UTC'));
        $stmt->bindValue('lastUpdated', $lastUpdated->format('Y-m-d H:i:s'));

        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * @param array $boxer
     * @param int $divisionId
     * @return bool
     * @throws DBALException|DbException
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

        $rowCount = $stmt->executeStatement();

        return $rowCount >= 0;
    }

    /**
     * @param array $boxerIds
     * @param int $divisionId
     * @throws DBALException
     */
    private function cleanupRatings(array $boxerIds, int $divisionId): void
    {
        $delete = 'DELETE FROM `rating` WHERE `boxer_id` NOT IN (?) AND `division_id` = ?';

        $this->connection->executeStatement($delete,
            [$boxerIds, $divisionId],
            [Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]
        );
    }

    /**
     * @param int $divisionId
     * @return bool
     * @throws DBALException
     */
    private function dropRatings(int $divisionId): bool
    {
        $delete = 'DELETE FROM `rating` WHERE `division_id` = ?';

        return (bool)$this->connection->executeStatement($delete, [$divisionId]);
    }
}