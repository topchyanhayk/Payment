<?php

namespace App\Services\Plan\Dto;

class CreatePlanDto
{
    public string $clientId;
    public string $name;
    public string $type;
    public int $price;
    public string $currency;
    public int $interval;

    public function __construct(
        string $clientId,
        string $name,
        string $type,
        int $price,
        string $currency,
        int $interval = 1
    ) {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->currency = $currency;
        $this->interval = $interval;
    }
}
