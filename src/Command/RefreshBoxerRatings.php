<?php

namespace App\Command;

use App\Entity\Division;
use App\Service\RatingsUpdater;
use App\Service\ServiceException;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshBoxerRatings extends Command
{
    /** @var string */
    protected static $defaultName = 'app:refresh-ratings';

    /** @var RatingsUpdater */
    private $ratingsUpdater;

    /** @var array */
    private $divisions;

    /**
     * @param RatingsUpdater $ratingsUpdater
     */
    public function __construct(RatingsUpdater $ratingsUpdater)
    {
        $this->ratingsUpdater = $ratingsUpdater;
        $this->divisions = Division::OPTIONS;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Refreshes the boxer ratings for each division.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ServiceException|Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->divisions as $division) {

            $output->writeln("<info>Updating boxer ratings for the $division division...</info>");

            if ($this->ratingsUpdater->update(new Division($division)) === false) {
                $output->writeln("<error>No ratings updated for the $division division!</error>");
            } else {
                $output->writeln("<info>Finished updating boxer ratings for the $division division.</info>");
            }

        }

        return 0;
    }
}