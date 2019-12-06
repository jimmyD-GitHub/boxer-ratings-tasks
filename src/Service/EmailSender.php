<?php

namespace App\Service;

use Exception;
use App\Entity\User;
use App\Repository\EmailRepository;
use Symfony\Component\Console\Output\OutputInterface;
use App\Adapter\MailAdapterFactoryInterface;

class EmailSender
{
    /** @var EmailRepository */
    private $repository;

    /** @var OutputInterface | null */
    private $outputWriter;

    /** @var MailAdapterFactoryInterface */
    private $mailAdapterFactory;

    /**
     * @param EmailRepository $emailRepository
     * @param MailAdapterFactoryInterface $mailAdapterFactory
     */
    public function __construct(EmailRepository $emailRepository, MailAdapterFactoryInterface $mailAdapterFactory)
    {
        $this->repository = $emailRepository;
        $this->mailAdapterFactory = $mailAdapterFactory;
    }

    /**
     * @return int
     * @throws ServiceException
     */
    public function processQueue(): int
    {
        $numSent = 0;

        foreach ($this->repository->findAllEmailsThatNeedToBeSent() as $email) {

            $user = $email->getUser();
            if ($user instanceof User === false) {
                throw new ServiceException('Could not find user for email queue ID: ' . $email->getId());
            }

            $emailType = $email->getType();
            $emailAddress = $user->getEmail();

            if ($this->outputWriter) {
                $this->outputWriter->writeln("Sending $emailType email to $emailAddress");
            }

            $mailAdapter = $this->mailAdapterFactory->fetchForEmail($email);
            if ($mailAdapter->send($user)) {

                try {
                    $this->repository->markEmailAsSent($email);
                } catch (Exception $exception) {
                    throw new ServiceException(
                        $exception->getMessage(),
                        $exception->getCode()
                    );
                }

                $numSent++;
            } else if ($this->outputWriter) {
                $this->outputWriter->writeln("Failed sending $emailType email to $emailAddress");
            }
        }

        return $numSent;
    }

    /**
     * @param OutputInterface $output
     * @return EmailSender
     */
    public function setOutputWriter(OutputInterface $output): EmailSender
    {
        $this->outputWriter = $output;

        return $this;
    }
}