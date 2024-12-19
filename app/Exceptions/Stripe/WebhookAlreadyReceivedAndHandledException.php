<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class WebhookAlreadyReceivedAndHandledException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return self::STRIPE_WEBHOOK_ALREADY_RECEIVED_AND_HANDLED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.webhook_already_received_and_handled');
    }
}
