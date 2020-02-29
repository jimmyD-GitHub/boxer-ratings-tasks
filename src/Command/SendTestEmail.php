<?php

namespace App\Command;

use App\Adapter\MailAdapter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendTestEmail extends Command
{
    /** @var string */
    protected static $defaultName = 'app:send-test-email';

    /** @var MailerInterface */
    private $mailer;

    /**
     * @param MailerInterface $mailer
     * @param string|null $name
     */
    public function __construct(MailerInterface $mailer, string $name = null)
    {
        $this->mailer = $mailer;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            'toAddress',
            InputArgument::REQUIRED,
            'The email address to send the test email to.'
        );

        $this->setDescription('Sends test email to verify SMTP settings.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Sending test email...</info>');

        $toAddress = new Address($input->getArgument('toAddress'), 'Test Recipient');
        $fromAddress = new Address(MailAdapter::FROM_ADDRESS, MailAdapter::FROM_NAME);

        $email = (new TemplatedEmail())
            ->to($toAddress)
            ->from($fromAddress)
            ->subject('Test Email')
            ->htmlTemplate('testEmail.html.twig')
            ->textTemplate('testEmail.txt.twig');

        try {
            $this->mailer->send($email);
            $output->writeln('<info>Email sent.</info>');
        } catch (TransportExceptionInterface $exception) {
            $output->writeln($exception->getMessage());
        }

        return 0;
    }
}