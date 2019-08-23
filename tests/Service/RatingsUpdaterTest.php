<?php

namespace App\Tests\Service;

use App\Entity\Division;
use App\Service\RatingsUpdater;
use App\Service\ServiceException;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as TestCase;

class RatingsUpdaterTest extends TestCase
{
    /** @var RatingsUpdater */
    private $service;

    /** @var Connection */
    private $connection;

    public function setUp(): void
    {
        self::bootKernel();

        $this->connection = self::$container->get('doctrine.dbal.default_connection');
        $this->service = new RatingsUpdater($this->connection);
    }

    /**
     * @throws ServiceException
     */
    public function testRatingsUpdate(): void
    {
        $this->assertTrue(
            $this->service->update(new Division('heavyweight'))
        );

        $ratings = $this->connection->fetchAll(
            'SELECT * FROM `rating` ORDER BY `points` DESC'
        );

        $this->assertEquals([
            0 => [
                'division_id' => '1',
                'boxer_id' => '8',
                'rating' => '1',
                'points' => '60'
            ],
            1 => [
                'division_id' => '1',
                'boxer_id' => '2',
                'rating' => '2',
                'points' => '54'
            ],
            2 => [
                'division_id' => '1',
                'boxer_id' => '1',
                'rating' => '3',
                'points' => '48'
            ]
        ], $ratings);
    }
}