<?php

namespace App\Command;

use App\Entity\Division;
use App\Service\RatingsUpdater;
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

    protected function configure(): void
    {
        $this->setDescription('Refreshes the boxer ratings for each division.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->divisions as $divisionId => $division) {

            $output->writeln("<info>Updating boxer ratings for the $division division...</info>");

            if ($this->ratingsUpdater->update($divisionId) === false) {
                $output->writeln("<error>Failed to update ratings for the $division division!</error>");
                break;
            }

            $output->writeln("<info>Finished updating boxer ratings for the $division division.</info>");
        }
    }
}