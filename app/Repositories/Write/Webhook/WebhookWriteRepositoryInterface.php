<?php

namespace App\Repositories\Write\Webhook;

use App\Models\Webhook\Webhook;

interface WebhookWriteRepositoryInterface
{
    public function save(Webhook $webhook): bool;
}
