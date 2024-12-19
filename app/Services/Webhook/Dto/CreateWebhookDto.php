<?php

namespace App\Services\Webhook\Dto;

use App\Services\Webhook\WebhookService;

class CreateWebhookDto
{
    public string $platformId;
    public string $platform;
    public string $status;

    public function __construct(
        string $platformId,
        string $platform,
        string $status = WebhookService::STATUS_RECEIVE_FROM_PLATFORM
    ) {
        $this->platformId = $platformId;
        $this->platform = $platform;
        $this->status = $status;
    }
}
