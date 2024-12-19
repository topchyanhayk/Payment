<?php

namespace App\Repositories\Write\Webhook;

use App\Exceptions\SavingErrorException;
use App\Models\Webhook\Webhook;

class WebhookWriteRepository implements WebhookWriteRepositoryInterface
{
    public function save(Webhook $webhook): bool
    {
        if (!$webhook->save()) {
            throw new SavingErrorException();
        }
        return true;
    }
}
