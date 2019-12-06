<?php

namespace App\Entity\EmailType;

class SignupConfirmation extends AbstractEmailType
{
    /**
     * @return string
     */
    public function getSubject(): string
    {
        return 'Confirm Your Sign Up To BoxerRatings.com';
    }

    /**
     * @return string
     */
    public function getHtmlTemplate(): string
    {
        return 'signupConfirmation.html.twig';
    }

    /**
     * @return string
     */
    public function getTextTemplate(): string
    {
        return 'signupConfirmation.txt.twig';
    }

    /**
     * @return string
     */
    public function getLinkUrl(): string
    {
        return $this->getBaseUrl() . '/verify-email/' . urlencode($this->getToken());
    }
}