<?php

namespace App\Adapter;

use App\Entity\Email;
use App\Entity\EmailType\ResetPassword;
use App\Entity\EmailType\SignupConfirmation;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailAdapterFactory implements MailAdapterFactoryInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var ContainerBagInterface */
    private $config;

    /**
     * @param MailerInterface $mailer
     * @param ContainerBagInterface $config
     */
    public function __construct(MailerInterface $mailer, ContainerBagInterface $config)
    {
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * @param Email $email
     * @return MailAdapterInterface
     */
    public function fetchForEmail(Email $email): MailAdapterInterface
    {
        $token = $email->getToken();
        $baseUrl = $this->config->get('app.ui_base_url');

        $emailType = new SignupConfirmation($baseUrl, $token);

        if ($email->getType() === ResetPassword::NAME) {
            $emailType = new ResetPassword($baseUrl, $token);
        }

        return new MailAdapter($this->mailer, $emailType);
    }
}