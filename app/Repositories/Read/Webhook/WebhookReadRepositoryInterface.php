<?php

namespace App\Repositories\Read\Webhook;

use App\Models\Webhook\Webhook;

interface WebhookReadRepositoryInterface
{
    public function getByPlatformId(string $webhookId): ?Webhook;
}
