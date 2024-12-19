<?php

namespace App\Repositories\Read\Webhook;

use App\Models\Webhook\Webhook;

class WebhookReadRepository implements WebhookReadRepositoryInterface
{
    public function getByPlatformId(string $webhookId): ?Webhook
    {
        return Webhook::where('platform_id', $webhookId)
            ->first();
    }
}
