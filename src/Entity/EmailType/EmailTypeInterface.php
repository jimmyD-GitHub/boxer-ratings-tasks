<?php

namespace App\Entity\EmailType;

interface EmailTypeInterface
{
    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return string
     */
    public function getHtmlTemplate(): string;

    /**
     * @return string
     */
    public function getTextTemplate(): string;

    /**
     * @return string
     */
    public function getLinkUrl(): string;
}
