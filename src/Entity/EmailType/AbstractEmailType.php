<?php

namespace App\Entity\EmailType;

abstract class AbstractEmailType implements EmailTypeInterface
{
    /** @var string */
    private $baseUrl;

    /** @var string */
    private $token;

    /**
     * @param string $baseUrl
     * @param string $token
     */
    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}