<?php

namespace App\Services\Subscription\Dto;

class CreateSubscriptionDto
{
    public string $planId;
    public string $platform;
    public string $clientSubscriptionId;
    public ?string $clientId;
    public ?string $email;
    public ?string $countryCode;
    public ?string $city;
    public ?string $postalCode;
    public ?string $line;
    public ?string $companyName;
    public ?string $phoneNumber;

    public function __construct(
        string $planId,
        string $platform,
        string $clientSubscriptionId,
        ?string $clientId = null,
        ?string $email = null,
        ?string $countryCode = null,
        ?string $city = null,
        ?string $postalCode = null,
        ?string $line = null,
        ?string $companyName = null,
        ?string $phoneNumber = null
    ) {
        $this->planId = $planId;
        $this->platform = $platform;
        $this->clientSubscriptionId = $clientSubscriptionId;
        $this->clientId = $clientId;
        $this->email = $email;
        $this->countryCode = $countryCode;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->line = $line;
        $this->companyName = $companyName;
        $this->phoneNumber = $phoneNumber;
    }
}
