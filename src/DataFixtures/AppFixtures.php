<?php

namespace App\DataFixtures;

use Doctrine\DBAL\Connection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Finder\Finder;

class AppFixtures extends Fixture
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
     * @param ObjectManager $manager
     * @throws DBALException
     */
    public function load(ObjectManager $manager): void
    {
        $manager->clear();

        $finder = new Finder();
        $finder->in(__DIR__ . '/../../vendor/jimmyd-github/boxer-ratings-mysql/schema')
            ->name('*.sql')
            ->sortByName()
            ->files();

        foreach ($finder as $file) {
            $sql = $file->getContents();
            $this->connection->exec($sql);
        }

        $manager->flush();
    }
}
