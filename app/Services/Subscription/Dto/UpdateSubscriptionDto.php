<?php

namespace App\Services\Subscription\Dto;

class UpdateSubscriptionDto
{
    public string $subscriptionId;
    public string $planId;

    public function __construct(
        string $subscriptionId,
        string $planId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->planId = $planId;
    }
}
