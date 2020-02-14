<?php

namespace App\Entity\EmailType;

class ResetPassword extends AbstractEmailType
{
    /** @var string */
    public const NAME = 'resetPassword';

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return 'Reset Your BoxerRatings.com Password';
    }

    /**
     * @return string
     */
    public function getHtmlTemplate(): string
    {
        return 'resetPassword.html.twig';
    }

    /**
     * @return string
     */
    public function getTextTemplate(): string
    {
        return 'resetPassword.txt.twig';
    }

    /**
     * @return string
     */
    public function getLinkUrl(): string
    {
        return $this->getBaseUrl() . '/reset-password/' . urlencode($this->getToken());
    }
}