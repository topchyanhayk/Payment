<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeSessionAlreadyPayedException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return self::STRIPE_SESSION_ALREADY_PAYED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.session_already_payed');
    }
}
