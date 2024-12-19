<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeSubscriptionDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::STRIPE_SUBSCRIPTION_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.subscription_does_not_exist');
    }
}
