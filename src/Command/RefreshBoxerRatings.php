<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshBoxerRatings extends Command
{
    /** @var string */
    protected static $defaultName = 'app:refresh-ratings';

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
        $output->writeln('<info>TODO: Implement boxer ratings refresh.</info>');
    }
}