<?php

namespace App\Adapter;

use App\Entity\User;

interface MailAdapterInterface
{
    /**
     * @param User $user
     * @return bool
     */
    public function send(User $user): bool;
}