<?php

namespace App\Adapter;

use App\Entity\Email;

interface MailAdapterFactoryInterface
{
    /**
     * @param Email $email
     * @return MailAdapterInterface
     */
    public function fetchForEmail(Email $email): MailAdapterInterface;
}