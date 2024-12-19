<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeWrongTypeWebhookException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::STRIPE_WRONG_TYPE_WEBHOOK;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.webhook_type_does_not_used');
    }
}
