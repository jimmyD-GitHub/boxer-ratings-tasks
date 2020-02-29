<?php

namespace App\Command;

use App\Service\EmailSender;
use App\Service\ServiceException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmails extends Command
{
    /** @var string */
    protected static $defaultName = 'app:send-emails';

    /** @var EmailSender */
    private $emailSender;

    /**
     * @param EmailSender $emailSender
     */
    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Sends emails that are queued up waiting to be sent.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ServiceException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->emailSender->setOutputWriter($output);
        $numSent = $this->emailSender->processQueue();

        $output->writeln('<info>Finished sending emails.</info>');
        $message = "Sent a total of $numSent " . ($numSent === 1 ? 'email.' : 'emails.');
        $output->writeln("<info>$message</info>");

        return 0;
    }
}