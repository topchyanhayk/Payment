<?php

namespace App\Services\Payment\Stripe\Dto;

class StripeWebhookDto
{
    public string $webhookId;
    public string $type;
    public ?string $subscriptionId;
    public ?string $objectId;

    public function __construct(
        string $webhookId,
        string $type,
        ?string $subscriptionId,
        ?string $objectId
    ) {
        $this->webhookId = $webhookId;
        $this->type = $type;
        $this->subscriptionId = $subscriptionId;
        $this->objectId = $objectId;
    }
}
