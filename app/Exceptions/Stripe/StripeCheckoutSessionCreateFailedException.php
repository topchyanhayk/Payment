<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeCheckoutSessionCreateFailedException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return self::STRIPE_CHECKOUT_SESSION_CREATE_FAILED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.create_checkout_session_failed');
    }
}
