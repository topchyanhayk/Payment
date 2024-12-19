<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeSubscriptionAlreadyCanceledException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return self::STRIPE_SUBSCRIPTION_ALREADY_CANCELED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.subscription_already_canceled');
    }
}
