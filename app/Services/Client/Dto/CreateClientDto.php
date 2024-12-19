<?php

namespace App\Services\Client\Dto;

class CreateClientDto
{
    public string $name;
    public string $email;
    public string $webhookUrl;
    public string $redirectUrl;
    public string $secret;

    public function __construct(
        string $name,
        string $email,
        string $secret,
        string $webhookUrl,
        string $redirectUrl
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->secret = $secret;
        $this->webhookUrl = $webhookUrl;
        $this->redirectUrl = $redirectUrl;
    }
}
