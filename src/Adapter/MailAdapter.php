<?php

namespace App\Adapter;

use App\Entity\User;
use App\Entity\EmailType\EmailTypeInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;

class MailAdapter implements MailAdapterInterface
{
    /** @var string */
    private const FROM_ADDRESS = 'noreply@boxeratings.com';

    /** @var string */
    private const FROM_NAME = 'BoxerRatings.com';

    /** @var MailerInterface */
    private $mailer;

    /** @var EmailTypeInterface */
    private $emailType;

    /**
     * @param MailerInterface $mailer
     * @param EmailTypeInterface $emailType
     */
    public function __construct(MailerInterface $mailer, EmailTypeInterface $emailType)
    {
        $this->mailer = $mailer;
        $this->emailType = $emailType;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function send(User $user): bool
    {
        $userName = $user->getName();
        $toAddress = new NamedAddress($user->getEmail(), $userName);
        $fromAddress = new NamedAddress(self::FROM_ADDRESS, self::FROM_NAME);

        $email = (new TemplatedEmail())
            ->to($toAddress)
            ->from($fromAddress)
            ->subject($this->emailType->getSubject())
            ->htmlTemplate($this->emailType->getHtmlTemplate())
            ->textTemplate($this->emailType->getTextTemplate())
            ->context([
                'name' => $userName,
                'url' => $this->emailType->getLinkUrl()
            ]);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $exception) {
            //TODO: log error
            return false;
        }
    }
}